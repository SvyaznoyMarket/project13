;(function($) {
	$(function(){
		var bodyElement = $('body'),
			compareElement = $('.js-compare'),
			tableElement = $('.js-compare-table', compareElement),
			headerElement = $('.js-compare-header'),
			footerElement = $('.js-compare-footer'),
			topbarElement = $('.js-topbar'),
			compareModel = createCompareModel(compareElement.data('compare-groups'));

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
			model.isSimilar = property.isSimilar;

			model.values = ko.observableArray();
			$.each(property.values, function(){
				model.values.push(createValueModel(this));
			});

			return model;
		}

		function createPropertyGroupModel(propertyGroup) {
			var model = {};
			model.name = propertyGroup.name;
			model.isSimilar = propertyGroup.isSimilar;

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
			model.onlySimilar = ko.observable(true);
			model.cart = ENTER.UserModel.cart;
			model.compare = ENTER.UserModel.compare;
			return model;
		}

		function createFixedTableCells(rowContainers, columnContainers, events) {
			if (!rowContainers[0]) {
				throw Error('Empty "rowContainers" in "createFixedTableCells" function');
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

			function calculateCellHeight(cells) {
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
						calculateCellHeight(rowContainers);
					}, 0);
				}

				if ((events || {}).onScrollEnd && !shifts.top && isScrollStarted) {
					isScrollStarted = false;
					events.onScrollEnd();
					calculateCellHeight(rowContainers);
				}
			}

			function resizeHandler() {
				offset = $(rowContainers.get(0)).offset();
				topOffset = offset.top;
				leftOffset = offset.left;
			}

			calculateCellHeight($($.unique($.merge($.makeArray(rowContainers), $.makeArray(columnContainers)))));
			windowElement.scroll(scrollHandler);
			windowElement.resize(resizeHandler);

			updatePosition();

			return {
				updatePosition: updatePosition,
				destroy: function(){
					windowElement.unbind('scroll', scrollHandler);
					windowElement.unbind('resize', resizeHandler);
				}
			};
		}

		ko.applyBindings(compareModel, compareElement[0]);

		var fixedTableCells = null;
		function initFixedTableCells() {
			if (fixedTableCells) {
				fixedTableCells.destroy();
			}

			fixedTableCells = createFixedTableCells(
				$('tr.js-compare-tableHeadRow td .js-compare-fixed, tr.js-compare-tableHeadRow th .js-compare-fixed', tableElement),
				$('th .js-compare-fixed', tableElement),
				{
					onScrollStart: function(){
						tableElement.addClass('cmprCnt-scroll');
					},
					onScrollEnd: function(){
						tableElement.removeClass('cmprCnt-scroll');
					}
				}
			);
		}

		compareModel.activeCompareGroupIndex.subscribe(function(){
			initFixedTableCells();
			compareModel.cart.valueHasMutated();
		});

		setTimeout(function(){
			initFixedTableCells();
		}, 0);

		compareElement.on('click', '.js-compare-deleteProductLink', function(e){
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
				}
			});
		});

		compareElement.on('click', '.js-compare-categoryLink', function(e){
			e.preventDefault();
			compareModel.activeCompareGroupIndex($(e.currentTarget).data('index'));
		});

		compareElement.on('click', '.js-compare-modeOnlySimilar', function(){
			compareModel.onlySimilar(true);
		});

		compareElement.on('click', '.js-compare-modeAll', function(){
			compareModel.onlySimilar(false);
		});

		compareElement.on('click', '.js-compare-propertyGroupLink', function(e){
			e.preventDefault();

			var rowElement = $(e.currentTarget).closest('tr');
			var tableElement = rowElement.closest('table');
			var className = 'cmprCnt_property_group-act';
			var isGroupStarted = false;

			if (rowElement.hasClass(className)) {
				rowElement.removeClass(className);

				$('>tbody>tr', tableElement).each(function() {
					if (this.rowIndex < rowElement[0].rowIndex) {
						return;
					}

					if (this.rowIndex == rowElement[0].rowIndex) {
						isGroupStarted = true;
						return;
					}

					if ($(this).hasClass('js-compare-propertyGroup')) {
						return false;
					}

					if (isGroupStarted) {
						$(this).removeClass('cmprCnt_property_group-hide');
					}
				});
			} else {
				rowElement.addClass(className);

				$('>tbody>tr', tableElement).each(function() {
					if (this.rowIndex < rowElement[0].rowIndex) {
						return;
					}

					if (this.rowIndex == rowElement[0].rowIndex) {
						isGroupStarted = true;
						return;
					}

					if ($(this).hasClass('js-compare-propertyGroup')) {
						return false;
					}

					if (isGroupStarted) {
						$(this).addClass('cmprCnt_property_group-hide');
					}
				});
			}

			isGroupStarted = false;
		});

		$(window).scroll(function(){
			var scrollX = (window.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft) - 2;
			topbarElement.css('left', scrollX + 'px');
			headerElement.css('left', scrollX + 'px');
			footerElement.css('left', scrollX + 'px');
			
			bodyElement.addClass('compare-scrolled');
		});
	});
}(jQuery));