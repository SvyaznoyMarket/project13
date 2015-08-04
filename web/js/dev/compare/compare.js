;(function($) {
	$(function(){
		var $window = $(window),
			$document = $(document),
			$compare = $('.js-compare'),
			$content = $('.js-compare-content'),
			$header1 = $('.js-compare-header1'),
			$header2 = $('.js-compare-header2'),
			$footer = $('.js-compare-footer'),
			$table = $('.js-compare-table', $compare),
			compareModel = createCompareModel($compare.data('compare-groups'), $compare.data('active-compare-group-index')),
			fixedTableCells = null;

		function createProductModel(product) {
			return product;
		}

		function createTypeModel(type) {
			var model = {};
			model.name = type.name;
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
			model.type = createTypeModel(compareGroup.type);

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

		function createCompareModel(compareGroups, activeCompareGroupIndex) {
			var model = {};

			model.compareGroups = ko.observableArray();
			$.each(compareGroups, function(){
				model.compareGroups.push(createCompareGroupModel(this));
			});

			model.activeCompareGroupIndex = ko.observable(activeCompareGroupIndex);
			model.similarOnly = ko.observable(true);
			model.scrolled = ko.observable(false);
			model.cart = ENTER.UserModel.cart;
			model.compare = ENTER.UserModel.compare;
			return model;
		}

		function hideNotSimilarProperties() {
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

			compareModel.similarOnly(true);
		}

		function showNotSimilarProperties() {
			var compareGroups = compareModel.compareGroups();
			if (compareGroups.length) {
				$.each(compareGroups[compareModel.activeCompareGroupIndex()].propertyGroups(), function(key, value){
					$.each(value.properties(), function(key, value){
						value.isSimilar(false);
					});

					value.isSimilar(false);
				});
			}

			compareModel.similarOnly(false);
		}

		function createFixedTableCells(rowContainers, columnContainers, events) {
			if (!rowContainers[0]) {
				return null;
			}

			var firstContainerCell = $(rowContainers[0]).closest('th, td'),
				offset = firstContainerCell.offset(),
				windowElement = $(window),
				sameContainers = $(),
				isVerticalScrollStarted = false,
				isHorizontalScrollStarted = false,
				isVerticalScrollEventDispatched = true,
				isHorizontalScrollEventDispatched = true;

			rowContainers.each(function() {
				var rowContainer = this;
				columnContainers.each(function() {
					if (rowContainer === this) {
						rowContainers = rowContainers.not(this);
						columnContainers = columnContainers.not(this);
						sameContainers = sameContainers.add(this);
					}
				});
			});

			rowContainers.css('z-index', '110');
			columnContainers.css('z-index', '120');
			sameContainers.css('z-index', '130');

			function updateSize(containers) {
				if (!containers) {
					containers = $($.merge($.makeArray(rowContainers), $.makeArray(columnContainers), $.makeArray(sameContainers)));
				}

				var containerStyles = {};
				containers.each(function(key) {
					$(this).attr('style', function(i, style) {
						return (style || '').replace(/height:[^;]+;?/g, '');
					});

					$(this).closest('th, td').attr('style', function(i, style) {
						return (style || '').replace(/height:[^;]+;?/g, '');
					});

					containerStyles[key] = $(this).attr('style');
					$(this).css({
						'position': 'relative',
						'left': 'auto',
						'top': 'auto',
						'margin-left': '0',
						'margin-top': '0'
					});
				});

				containers.each(function(key) {
					var container = $(this),
						cell = container.closest('th, td'),
						cellHeight = cell.height(),
						containerFullHeight = container.outerHeight(false);

					$(this).attr('style', containerStyles[key]);

					if (cellHeight > containerFullHeight) {
						var containerPaddingTop = parseInt(container.css('padding-top')) || 0;

						if (cell.css('vertical-align') == 'middle') {
							container.css('padding-top', Math.round((cellHeight - containerFullHeight) / 2) + containerPaddingTop + 'px');
						} else if (cell.css('vertical-align') == 'bottom') {
							container.css('padding-top', Math.round((cellHeight - containerFullHeight)) + containerPaddingTop + 'px');
						}
					}

					container.css('height', cellHeight + 'px');
					cell.css({'height': cellHeight + 'px', 'vertical-align': 'top'});
				});
			}

			function getShift() {
				var scrollY = $window.scrollTop(),
					scrollX = $window.scrollLeft(),
					top = 0,
					left = 0;

				if (scrollY > offset.top) {
					top = scrollY - offset.top;
				}

				if (scrollX > offset.left) {
					left = scrollX - offset.left;
				}

				return {
					top: top,
					left: left
				};
			}

			function updatePosition() {
				var shift = getShift();

				if (shift.top) {
					if (!isVerticalScrollStarted) {
						isVerticalScrollStarted = true;
						isVerticalScrollEventDispatched = false;

						if ((events || {}).onVerticalScrollStart) {
							events.onVerticalScrollStart();
						}

						setTimeout(function() {
							updateSize($($.merge($.makeArray(rowContainers), $.makeArray(sameContainers))));

							rowContainers.css({
								'position': 'fixed',
								'top': '0',
								'margin-left': -shift.left + 'px'
							});

							sameContainers.css({
								'position': 'fixed',
								'top': '0',
								'margin-top': '0'
							});
						}, 0);
					} else {
						setTimeout(function() {
							rowContainers.css('margin-left', -shift.left + 'px');
						}, 0);
					}

					if (!isHorizontalScrollStarted) {
						setTimeout(function() {
							sameContainers.css('margin-left', -shift.left + 'px');
						}, 0);
					}
				}

				if (!shift.top && !isVerticalScrollEventDispatched && $document.height() - ($window.scrollTop() + $window.height()) > 0) {
					isVerticalScrollEventDispatched = true;

					if ((events || {}).onVerticalScrollEnd) {
						events.onVerticalScrollEnd();
					}

					if (!isVerticalScrollStarted) {
						setTimeout(function() {
							updateSize($($.merge($.makeArray(rowContainers), $.makeArray(sameContainers))));
						}, 0);
					}
				}

				if (!shift.top && isVerticalScrollStarted) {
					isVerticalScrollStarted = false;

					setTimeout(function() {
						rowContainers.css({
							'position': 'relative',
							'top': 'auto',
							'margin-left': '0'
						});

						sameContainers.css({
							'top': 'auto',
							'margin-left': '0'
						});

						setTimeout(function() {
							updateSize($($.merge($.makeArray(rowContainers), $.makeArray(sameContainers))));
						}, 0);
					}, 0);
				}

				if (shift.left) {
					if (!isHorizontalScrollStarted) {
						isHorizontalScrollStarted = true;
						isHorizontalScrollEventDispatched = false;

						if ((events || {}).onHorizontalScrollStart) {
							events.onHorizontalScrollStart();
						}

						setTimeout(function() {
							columnContainers.css({
								'position': 'fixed',
								'left': '0'
							});

							sameContainers.css({
								'position': 'fixed',
								'left': '0',
								'margin-left': '0'
							});
						}, 0);
					}

					setTimeout(function() {
						columnContainers.css('margin-top', -$window.scrollTop() + 'px');
					}, 0);

					if (!isVerticalScrollStarted) {
						setTimeout(function() {
							sameContainers.css('margin-top', -$window.scrollTop() + 'px');
						}, 0);
					}
				}

				if (!shift.left && !isHorizontalScrollEventDispatched && $document.width() - ($window.scrollLeft() + $window.width()) > 0) {
					isHorizontalScrollEventDispatched = true;

					if ((events || {}).onHorizontalScrollEnd) {
						events.onHorizontalScrollEnd();
					}
				}

				if (!shift.left && isHorizontalScrollStarted) {
					isHorizontalScrollStarted = false;

					setTimeout(function() {
						columnContainers.css({
							'position': 'relative',
							'left': 'auto',
							'margin-top': '0'
						});

						sameContainers.css({
							'left': 'auto',
							'margin-top': '0'
						});
					}, 0);
				}

				if (!shift.top && !shift.left) {
					setTimeout(function() {
						sameContainers.css('position', 'relative');
					}, 0);
				}
			}

			function resizeHandler() {
				offset = firstContainerCell.offset();
			}

			updateSize();
			updatePosition();

			windowElement.scroll(updatePosition);
			windowElement.resize(resizeHandler);

			return {
				updateSize: updateSize,
				destroy: function(){
					windowElement.unbind('scroll', updatePosition);
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
					onVerticalScrollStart: function(){
						compareModel.scrolled(true);
					},
					onVerticalScrollEnd: function(){
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


		function updateElements(){
			var
				documentHeight = $document.height(),
				windowHeight = $window.height(),
				scrollTop = $window.scrollTop(),
				footerFixedTop = windowHeight - $footer.outerHeight(false),
				footerTop = footerFixedTop + (documentHeight - windowHeight - scrollTop);

			$content.css({
				'padding-top': $header1.outerHeight(false) + $header2.outerHeight(false)
			});

			$header1.css({
				'margin-top': (scrollTop >= 0 ? -scrollTop : 0) + 'px'
			});

			$header2.css({
				'top': $header1.outerHeight(false) + 'px',
				'margin-top': (scrollTop >= 0 ? -scrollTop : 0) + 'px'
			});

			$footer.css({
				'top': (footerTop > footerFixedTop ? footerTop : footerFixedTop) + 'px'
			});
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
			updateElements();
		});

		compareModel.similarOnly.subscribe(function(){
			if (fixedTableCells) {
				fixedTableCells.updateSize();
			}

			updateElements();
		});

		$.each(compareModel.compareGroups(), function(key, value) {
			$.each(value.propertyGroups(), function(key, value) {
				value.opened.subscribe(function(){
					if (fixedTableCells) {
						fixedTableCells.updateSize();
					}

					updateElements();
				});
			});
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
					updateElements();
					if (fixedTableCells) {
						fixedTableCells.updateSize(); // Иначе после удаления товара иногда оставшиеся товары не сдвигаются влево (на место удалённого товара)
					}
				}
			});
		});

		$compare.on('click', '.js-compare-typeLink', function(e){
			e.preventDefault();
			compareModel.activeCompareGroupIndex($(e.currentTarget).data('index'));
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

		$(window).resize(updateElements);
		$(window).scroll(updateElements);

		updateElements();
	});
}(jQuery));