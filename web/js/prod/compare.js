;(function($) {
	$(function(){
		var $body = $('body'),
			$compare = $('.js-compare'),
			$header = $('.js-compare-header'),
			$footer = $('.js-compare-footer'),
			$table = $('.js-compare-table', $compare),
			$topbar = $('.js-topbar'),
			compareModel = createCompareModel($compare.data('compare-groups')),
			fixedTableCells = null;

		function createProductModel(product) {
			var model = {};
			model.id = product.id;
			model.prefix = product.prefix;
			model.webName = product.webName;
			model.link = product.link;
			model.price = product.price;
			model.priceOld = product.priceOld;
			model.inShopOnly = product.inShopOnly;
			model.inShopShowroomOnly = product.inShopShowroomOnly;
			model.isBuyable = product.isBuyable;
			model.statusId = product.statusId;
			model.imageUrl = product.imageUrl;
			model.reviews = product.reviews;
			model.deleteFromCompareUrl = product.deleteFromCompareUrl;
			model.upsale = product.upsale;
			return model;
		}

		function createCategoryModel(category) {
			var model = {};
			model.name = category.name;
			return model;
		}

		function createValueModel(property) {
			var model = {};
			model.text = property.text;
			model.productId = property.productId;
			return model;
		}

		function createPropertyModel(property) {
			var model = {};
			model.name = property.name;
			model.isSimilar = ko.observable();

			model.values = ko.observableArray();
			$.each(property.values, function(){
				model.values.push(createValueModel(this));
			});

			return model;
		}

		function createPropertyGroupModel(propertyGroup) {
			var model = {};
			model.name = propertyGroup.name;
			model.isSimilar = ko.observable();
			model.opened = ko.observable(true);

			model.properties = ko.observableArray();
			$.each(propertyGroup.properties, function(){
				model.properties.push(createPropertyModel(this));
			});

			return model;
		}

		function createCompareGroupModel(compareGroup) {
			var model = {};
			model.category = createCategoryModel(compareGroup.category);

			model.products = ko.observableArray();
			$.each(compareGroup.products, function(){
				model.products.push(createProductModel(this));
			});

			model.propertyGroups = ko.observableArray();
			$.each(compareGroup.propertyGroups, function(){
				model.propertyGroups.push(createPropertyGroupModel(this));
			});

			return model;
		}

		function createCompareModel(compareGroups) {
			var model = {};

			model.compareGroups = ko.observableArray();
			$.each(compareGroups, function(){
				model.compareGroups.push(createCompareGroupModel(this));
			});

			model.activeCompareGroupIndex = ko.observable(0);
			model.similarOnly = ko.observable(true);
			model.scrolled = ko.observable(false);
			model.cart = ENTER.UserModel.cart;
			model.compare = ENTER.UserModel.compare;
			return model;
		}

		function hideNotSimilarProperties() {
			compareModel.similarOnly(true);

			var compareGroups = compareModel.compareGroups();
			if (compareGroups.length) {
				$.each(compareGroups[compareModel.activeCompareGroupIndex()].propertyGroups(), function(key, value){
					var isSimilarGroup = true;

					$.each(value.properties(), function(key, value){
						var isSimilarProperty = true,
							previousValueText = null;

						$.each(value.values(), function(key, value){
							if (value.text != previousValueText && previousValueText !== null) {
								isSimilarGroup = false;
								isSimilarProperty = false;
								return false;
							}

							previousValueText = value.text;
						});

						value.isSimilar(isSimilarProperty);
					});

					value.isSimilar(isSimilarGroup);
				});
			}
		}

		function showNotSimilarProperties() {
			compareModel.similarOnly(false);

			var compareGroups = compareModel.compareGroups();
			if (compareGroups.length) {
				$.each(compareGroups[compareModel.activeCompareGroupIndex()].propertyGroups(), function(key, value){
					$.each(value.properties(), function(key, value){
						value.isSimilar(false);
					});

					value.isSimilar(false);
				});
			}
		}

		function createFixedTableCells(rowContainers, columnContainers, events) {
			if (!rowContainers[0]) {
				return null;
			}

			var offset = $(rowContainers[0]).offset(),
				topOffset = offset.top,
				leftOffset = offset.left,
				windowElement = $(window);

			rowContainers.css('position', 'relative');
			rowContainers.css('z-index', '110');

			columnContainers.css('position', 'relative');
			columnContainers.css('z-index', '120');

			rowContainers.each(function(){
				var rowContainer = this;
				columnContainers.each(function(){
					if (rowContainer === this) {
						$(this).css('z-index', '130');
					}
				});
			});

			function updateCellHeight(cells) {
				if (!cells) {
					cells = $($.unique($.merge($.makeArray(rowContainers), $.makeArray(columnContainers))));
				}

				cells.each(function(){
					$(this).css('min-height', '0');
				});

				cells.each(function(){
					var container = $(this);

					var cell = container.closest('th, td');

					var cellHeight = cell.height();
					var containerHeight = container.height();

					var containerPaddingTop = parseInt(container.css('padding-top')) || 0;
					var containerPaddingBottom = parseInt(container.css('padding-bottom')) || 0;

					var containerBorderTop = parseInt(container.css('border-top-width')) || 0;
					var containerBorderBottom = parseInt(container.css('border-bottom-width')) || 0;

					var containerFullHeight = containerHeight + containerPaddingTop + containerPaddingBottom + containerBorderTop + containerBorderBottom;

					var newContainerPaddingTop = containerPaddingTop;

					if (cellHeight > containerFullHeight) {
						if (cell.css('vertical-align') == 'middle') {
							newContainerPaddingTop = Math.round((cellHeight - containerFullHeight) / 2) + containerPaddingTop;
							container.css('padding-top', newContainerPaddingTop + 'px');
						} else if (cell.css('vertical-align') == 'bottom') {
							newContainerPaddingTop = Math.round((cellHeight - containerFullHeight)) + containerPaddingTop;
							container.css('padding-top', newContainerPaddingTop + 'px');
						}
					}

					container.css('min-height', cellHeight + 'px');
				});
			}

			function updatePosition() {
				var scrollY = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop,
					scrollX = window.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft,
					top = null,
					left = null;

				if (scrollY > topOffset) {
					top = scrollY - topOffset;
					rowContainers.css('top', top + 'px');
				} else {
					rowContainers.css('top', 'auto');
				}

				if (scrollX > leftOffset) {
					left = scrollX - leftOffset;
					columnContainers.css('left', left + 'px');
				} else {
					columnContainers.css('left', 'auto');
				}

				return {
					top: top,
					left: left
				};
			}

			var isScrollStarted = false;
			function scrollHandler() {
				var shifts = updatePosition();

				if ((events || {}).onScrollStart && shifts.top && !isScrollStarted) {
					isScrollStarted = true;
					events.onScrollStart();
					setTimeout(function(){
						updateCellHeight(rowContainers);
					}, 0);
				}

				if ((events || {}).onScrollEnd && !shifts.top && isScrollStarted) {
					isScrollStarted = false;
					events.onScrollEnd();
					updateCellHeight(rowContainers);
				}
			}

			function resizeHandler() {
				offset = $(rowContainers.get(0)).offset();
				topOffset = offset.top;
				leftOffset = offset.left;
			}

			updateCellHeight();
			windowElement.scroll(scrollHandler);
			windowElement.resize(resizeHandler);

			updatePosition();

			return {
				updatePosition: updatePosition,
				updateCellHeight: updateCellHeight,
				destroy: function(){
					windowElement.unbind('scroll', scrollHandler);
					windowElement.unbind('resize', resizeHandler);
				}
			};
		}

		function initFixedTableCells() {
			if (fixedTableCells) {
				fixedTableCells.destroy();
			}

			fixedTableCells = createFixedTableCells(
				$('tr.js-compare-tableHeadRow td .js-compare-fixed, tr.js-compare-tableHeadRow th .js-compare-fixed', $table),
				$('th .js-compare-fixed', $table),
				{
					onScrollStart: function(){
						compareModel.scrolled(true);
					},
					onScrollEnd: function(){
						compareModel.scrolled(false);
					}
				}
			);
		}

		function updateSimilarPropertiesDisplay() {
			if (compareModel.similarOnly()) {
				hideNotSimilarProperties();
			} else {
				showNotSimilarProperties();
			}
		}

		updateSimilarPropertiesDisplay();
		ko.applyBindings(compareModel, $compare[0]);
		setTimeout(function(){
			initFixedTableCells();
		}, 0);

		compareModel.activeCompareGroupIndex.subscribe(function(){
			initFixedTableCells();
			compareModel.cart.valueHasMutated();
			updateSimilarPropertiesDisplay();
		});

		compareModel.similarOnly.subscribe(function(){
			if (fixedTableCells) {
				fixedTableCells.updateCellHeight();
			}
		});

		$compare.on('click', '.js-compare-deleteProductLink', function(e){
			e.preventDefault();
			var anchor = e.currentTarget;
			$.ajax({
				url: e.currentTarget.href,
				success: function() {
					var productId = $(anchor).data('product-id');

					compareModel.compareGroups.remove(function(item){
						item.products.remove(function(item){
							return item.id == productId;
						});

						item.propertyGroups.remove(function(item){
							item.properties.remove(function(item){
								item.values.remove(function(item){
									return item.productId == productId;
								});

								return item.values().length == 0;
							});

							return item.properties().length == 0;
						});

						return item.products().length == 0;
					});
					
					compareModel.compare.remove(function(item){ return item.id == productId; });

					var compareGroupsLength = compareModel.compareGroups().length;
					if (compareModel.activeCompareGroupIndex() > compareGroupsLength - 1) {
						compareModel.activeCompareGroupIndex(compareGroupsLength - 1);
					}

					updateSimilarPropertiesDisplay();
				}
			});
		});

		$compare.on('click', '.js-compare-categoryLink', function(e){
			e.preventDefault();
			compareModel.activeCompareGroupIndex($(e.currentTarget).data('index'));
			$(window).scroll();
		});

		$compare.on('click', '.js-compare-modeSimilarOnly', function(){
			hideNotSimilarProperties();
		});

		$compare.on('click', '.js-compare-modeAll', function(){
			showNotSimilarProperties();
		});

		$compare.on('click', '.js-compare-propertyGroupLink', function(e){
			e.preventDefault();

			var compareGroups = compareModel.compareGroups();
			if (!compareGroups.length) {
				return;
			}

			var propertyGroups = compareGroups[compareModel.activeCompareGroupIndex()].propertyGroups(),
				propertyGroupIndex = $(e.currentTarget).closest('tr').data('property-group-index');

			if (propertyGroups[propertyGroupIndex].opened()) {
				propertyGroups[propertyGroupIndex].opened(false);
			} else {
				propertyGroups[propertyGroupIndex].opened(true);
			}
		});

		$(window).scroll(function(){
			var scrollX = window.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft,
				maxScrollX = $table.width() - $body.width();

			if (maxScrollX > 0) {
				if (scrollX < maxScrollX) {
					$topbar.css('left', scrollX + 'px');
					$header.css('left', scrollX + 'px');
					$footer.css('left', scrollX + 'px');
				} else if (scrollX >= maxScrollX) {
					$topbar.css('left', maxScrollX + 'px');
					$header.css('left', maxScrollX + 'px');
					$footer.css('left', maxScrollX + 'px');
				}
			} else {
				$topbar.css('left', '0');
				$header.css('left', '0');
				$footer.css('left', '0');
			}
		});
	});
}(jQuery));