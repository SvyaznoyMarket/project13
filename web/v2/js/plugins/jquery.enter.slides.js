(function( window, $, undefined ) {

  $.enterslide = function( options, element ) {
    this.$el = $( element );
    this._init( options );
  };
  
  $.enterslide.defaults = {
    minItems  : 1,  // минимальное количество показываемых слайдов
    current   : 0,  // индекс текущего элемента
    onClick   : function() { return false; } // callback
  };
  
  $.enterslide.prototype   = {
    _init : function( options ) {
      this.options    = $.extend( true, {}, $.enterslide.defaults, options );
      this.$slider = this.$el.find('.sliderBox_inner'); // sliderBox_inner
      this.$list = this.$slider.find('.sliderBoxItems'); // список товаров (ul)
      this.$items  = this.$list.children('.sliderBoxItems_item'); // элемент списка товаров (ul > li)
      this.itemsCount = this.$items.length; // количество элементов в списке

      this._configure(); // установка парметров слайдера
      this._addControls(); // добавляем стрелки слайдера
      this._initEvents(); // пересчет параметров, скрытие показ кнопок
      this._slideToCurrent( false ); //текущая позиция слайдера

      var slideLeft = 0;
    },

    /**
      * Метод конфигурации
      */
    _configure : function() {
      this.current = this.options.current;
      this.visibleWidth = this.$slider.width();
      
      // если прозошел resize, то пересчитываем параметры слайдера
      if( this.visibleWidth < this.options.minItems + ( this.options.minItems - 1 ) ) {
        this._setCurrentValues();
        this.fitCount = this.options.minItems;
      }
      else {
        this._setCurrentValues();
      }
    
      this.$list.css({width : this.sliderW}); // ширина списка ul
    },

    /**
      * Метод расчета параметров слайдера
      */
    _setCurrentValues : function() {
      this.itemW  = this.$items.outerWidth(true); // ширина элемента списка
      this.sliderW  = this.itemW * this.itemsCount; // ширина списка ul
      this.visibleWidth = this.$slider.width(); // ширина sliderBox_inner
      this.fitCount   = Math.floor( this.visibleWidth / this.itemW ); // количество видимых элементов списка
    },

    /**
     * Метод добавления контролов
     */
    _addControls : function() {
      this.$navNext = $('<span class="sliderControls_btn sliderControls_btn__right"></span>');
      this.$navPrev = $('<span class="sliderControls_btn sliderControls_btn__left"></span>');
      $('<div class="sliderControls"/>')
      .append( this.$navPrev )
      .append( this.$navNext )
      .appendTo( this.$el );
    },

    /**
     * Метод проверки показа контролов
     */
    _toggleControls : function( dir, status ) {
      if( dir && status ) {
        if( status === 1 )
          ( dir === 'right' ) ? this.$navNext.show() : this.$navPrev.show();
        else
          ( dir === 'right' ) ? this.$navNext.hide() : this.$navPrev.hide();
      }
      else if( this.current === this.itemsCount - 1 || this.fitCount >= this.itemsCount )
          this.$navNext.hide();
    },

    /**
     * Метод инициализации событий (resize, клики по контролам)
     */
    _initEvents : function() {
      var instance  = this;
      
      // window resize
      $(window).on('resize.enterslide', function( event ) {
        instance._reload();
        instance._slideToCurrent();
      });
      
      // клики по контролам
      this.$navNext.on('click.enterslide', function( event ) {
        instance._slide('right');
      });
      
      this.$navPrev.on('click.enterslide', function( event ) {
        instance._slide('left');
      });
      
      // touch
      instance.$list.touchwipe({
        wipeLeft : function() {
          instance._slide('right');
        },
        wipeRight : function() {
          instance._slide('left');
        }
      });
      
    },

    /**
     * Метод получения параметров после resize
     */
    _reload : function() {
      var instance  = this;

      instance._setCurrentValues();
      
      // пересчитываем параметры слайдера
      if( instance.visibleWidth < instance.options.minItems + ( instance.options.minItems - 1 ) ) {
        instance._setCurrentValues();
        instance.fitCount = instance.options.minItems;
      } 
      else{
        instance._setCurrentValues();
      }
    },

    _slide : function( dir ) {
      // текущее значение прокрутки
        var slideLeft = parseFloat( this.$list.css('left') );
        var amount  = this.fitCount * this.itemW;

        console.log('nextSlides');
            console.log(this.sliderW);
            console.log('slideLeft' + slideLeft);
            console.log(this.visibleWidth);
        
        if( dir === 'right' ) {
          //btnPrev.show();

          if ( this.sliderW - ( Math.abs( slideLeft ) + amount ) < this.visibleWidth ) {
            slideLeft = this.$list.width() - this.fitCount * this.itemW;

            console.log('nextSlides');
            console.log(this.sliderW);
            console.log('slideLeft' + slideLeft);
            console.log(this.visibleWidth);

          }
          else {
            slideLeft = slideLeft - this.fitCount * this.itemW;

            console.log('nextSlides');
            console.log(this.sliderW);
            console.log(this.itemW);
            console.log('slideLeft' + slideLeft);
            console.log(this.visibleWidth);
          }

          this.$list.stop(true, false).animate({'left' : slideLeft});

          console.log('nextSlides' + slideLeft);
          console.log(this.fitCount);

          return false;
        }

        else if( dir === 'left') {        
          console.log('prevSlides');

          //btnNext.show();
          slideLeft  = Math.abs( slideLeft );
          console.log(slideLeft);

          if ( slideLeft - this.fitCount * this.itemW <= 0 ) {
            slideLeft = 0;
            //btnPrev.hide();
          }
          else {
            slideLeft = slideLeft - this.fitCount * this.itemW;
            //btnPrev.show();
          }

          this.$list.stop(true, false).animate({'left' : -slideLeft});

          console.log('prevSlides' + slideLeft);

          return false;
        }
    },

    _slideToCurrent : function( anim ) {
      var amount  = this.current * this.itemW;
      this._slide('', -amount, anim );
    },

    setCurrent : function( idx, callback ) {
      
      this.current = idx;
      
      var mleft    = Math.abs( parseFloat( this.$list.css('margin-left') ) ),
        posR  = mleft + this.visibleWidth,
        fmleft   = Math.abs( this.current * this.itemW );
      
      if( fmleft + this.itemW > posR || fmleft < mleft ) {
        this._slideToCurrent();
      }
      
      if ( callback ) callback.call();
      
    },

    destroy : function( callback ) {
      
      this._destroy( callback );
      
    },

    _destroy : function( callback ) {
      this.$el.off('.enterslide').removeData('enterslide');
      $(window).off('.enterslide');
      if ( callback ) callback.call();
    }
  };
  
  $.fn.enterslide = function( options ) {
    if ( typeof options === 'string' ) {
      var args = Array.prototype.slice.call( arguments, 1 );

      this.each(function() {
        var instance = $.data( this, 'enterslide' );

        instance[ options ].apply( instance, args );
      });
    } 
    else {
      this.each(function() {
        var instance = $.data( this, 'enterslide' );
        if ( !instance ) {
          $.data( this, 'enterslide', new $.enterslide( options, this ) );
        }
      });
    }
    return this;
  };
  
})( window, jQuery );