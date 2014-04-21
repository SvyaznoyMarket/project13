(function( window, $, undefined ) {
  
  $.enterslide = function( options, element ) {
    this.$el  = $( element );
    this._init( options );
  };
  
  $.enterslide.defaults  = {
    imageW    : 190, 
    margin    : 3,  
    minItems  : 1, 
    onClick   : function() { return false; }
    };
  
  $.enterslide.prototype   = {
    _init         : function( options ) {
      
      this.options    = $.extend( true, {}, $.enterslide.defaults, options );
      
      // <ul>
      this.$slider    = this.$el.find('ul');
      
      // <li>
      this.$items     = this.$slider.children('li');
      
      // total number of elements / images
      this.itemsCount   = this.$items.length;
      
      // cache the <ul>'s parent, since we will eventually need to recalculate its width on window resize
      this.$esCarousel  = this.$slider.parent();
      
      // set sizes and initialize some vars...
      this._configure();
      
      // add navigation buttons
      this._addControls();
      
      // initialize the events
      this._initEvents(); 
    },

    _configure      : function() {
      
      // current item's index
      this.current    = this.options.current;
      
      // the ul's parent's (div.es-carousel) width is the "visible" width
      this.visibleWidth = this.$esCarousel.width();
      
      // test to see if we need to initially resize the items
      if( this.visibleWidth < this.options.minItems * ( this.options.imageW ) + ( this.options.minItems - 1 ) * this.options.margin ) {
        this._setCurrentValues();
        // how many items fit with the current width
        this.fitCount = this.options.minItems;
      }
      else {
        this._setCurrentValues();
      }
      
      // set the <ul> width
      this.$slider.css({
        width : this.sliderW
      });
      
    },

    _setCurrentValues : function() {
      
      // the total space occupied by one item
      this.itemW      = this.$items.outerWidth(true);
      
      // total width of the slider / <ul>
      // this will eventually change on window resize
      this.sliderW    = this.itemW * this.itemsCount;
      
      // the ul parent's (div.es-carousel) width is the "visible" width
      this.visibleWidth = this.$esCarousel.width();
      
      // how many items fit with the current width
      this.fitCount   = Math.floor( this.visibleWidth / this.itemW );
      
    },

    _addControls    : function() {
      
      this.$navNext = $('<span class="btn">Next</span>');
      this.$navPrev = $('<span class="btn">Previous</span>');
      $('<div class="sliderControls"/>')
      .append( this.$navPrev )
      .append( this.$navNext )
      .appendTo( this.$el );
      
      //this._toggleControls();
        
    },

    _toggleControls   : function( dir, status ) {
      
      // show / hide navigation buttons
      if( dir && status ) {
        if( status === 1 )
          ( dir === 'right' ) ? this.$navNext.show() : this.$navPrev.show();
        else
          ( dir === 'right' ) ? this.$navNext.hide() : this.$navPrev.hide();
      }
      else if( this.current === this.itemsCount - 1 || this.fitCount >= this.itemsCount )
          this.$navNext.hide();
      
    },

    _initEvents     : function() {
      
      var instance  = this;
      
      // window resize
      $(window).on('resize.enterslide', function( event ) {
        
        instance._reload();
        
        // slide to the current element
        clearTimeout( instance.resetTimeout );
        instance.resetTimeout = setTimeout(function() {
          instance._slideToCurrent();
        }, 200);
        
      });
      
      // navigation buttons events
      this.$navNext.on('click.enterslide', function( event ) {
        instance._slide('right');
      });
      
      this.$navPrev.on('click.enterslide', function( event ) {
        instance._slide('left');
      });
      
      // item click event
      this.$slider.on('click.enterslide', 'li', function( event ) {
        instance.options.onClick( $(this) );
        return false;
      });
      
      // touch events
      instance.$slider.touchwipe({
        wipeLeft      : function() {
          instance._slide('right');
        },
        wipeRight     : function() {
          instance._slide('left');
        }
      });
      
    },

    reload        : function( callback ) {
      this._reload();
      if ( callback ) callback.call();
    
    },

    _reload       : function() {
      
      var instance  = this;
      
      // set values again
      instance._setCurrentValues();
      
      // need to resize items
      if( instance.visibleWidth < instance.options.minItems * instance.options.imageW + ( instance.options.minItems - 1 ) * instance.options.margin ) {
        instance._setDim( ( instance.visibleWidth - ( instance.options.minItems - 1 ) * instance.options.margin ) / instance.options.minItems );
        instance._setCurrentValues();
        instance.fitCount = instance.options.minItems;
      } 
      else{
        instance._setDim();
        instance._setCurrentValues();
      }
      
      instance.$slider.css({
        width : instance.sliderW + 10 // TODO: +10px seems to solve a firefox "bug" :S
      });
      
    },

    _slide : function( dir, val, anim, callback ) {

      // current margin left
      var ml    = parseFloat( this.$slider.css('margin-left') );
      
      // val is just passed when we want an exact value for the margin left (used in the _slideToCurrent function)
      if( val === undefined ) {
      
        // how much to slide?
        var amount  = this.fitCount * this.itemW, val;
        
        if( amount < 0 ) return false;
        
        // make sure not to leave a space between the last item / first item and the end / beggining of the slider available width
        if( dir === 'right' && this.sliderW - ( Math.abs( ml ) + amount ) < this.visibleWidth ) {
          amount  = this.sliderW - ( Math.abs( ml ) + this.visibleWidth ) - this.options.margin; // decrease the margin left
          // show / hide navigation buttons
          this._toggleControls( 'right', -1 );
          this._toggleControls( 'left', 1 );
        }
        else if( dir === 'left' && Math.abs( ml ) - amount < 0 ) {        
          amount  = Math.abs( ml );
          // show / hide navigation buttons
          this._toggleControls( 'left', -1 );
          this._toggleControls( 'right', 1 );
        }
        else {
          var fml; // future margin left
          ( dir === 'right' ) 
            ? fml = Math.abs( ml ) + this.options.margin + Math.abs( amount ) 
            : fml = Math.abs( ml ) - this.options.margin - Math.abs( amount );
          
          // show / hide navigation buttons
          if( fml > 0 )
            this._toggleControls( 'left', 1 );
          else  
            this._toggleControls( 'left', -1 );
          
          if( fml < this.sliderW - this.visibleWidth )
            this._toggleControls( 'right', 1 );
          else  
            this._toggleControls( 'right', -1 );
            
        }
        
        ( dir === 'right' ) ? val = '-=' + amount : val = '+=' + amount
        
      }
      else {
        var fml   = Math.abs( val ); // future margin left
        
        if( Math.max( this.sliderW, this.visibleWidth ) - fml < this.visibleWidth ) {
          val = - ( Math.max( this.sliderW, this.visibleWidth ) - this.visibleWidth );
          if( val !== 0 )
            val += this.options.margin; // decrease the margin left if not on the first position
            
          // show / hide navigation buttons
          this._toggleControls( 'right', -1 );
          fml = Math.abs( val );
        }
        
        // show / hide navigation buttons
        if( fml > 0 )
          this._toggleControls( 'left', 1 );
        else
          this._toggleControls( 'left', -1 );
        
        if( Math.max( this.sliderW, this.visibleWidth ) - this.visibleWidth > fml + this.options.margin ) 
          this._toggleControls( 'right', 1 );
        else
          this._toggleControls( 'right', -1 );
          
      }
      
      $.fn.applyStyle = ( anim === undefined ) ? $.fn.animate : $.fn.css;
      
      var sliderCSS = { marginLeft : val };
      
      var instance  = this;
      
      this.$slider.stop().applyStyle( sliderCSS, $.extend( true, [], { complete : function() {
        if( callback ) callback.call();
      } } ) );
      
    },

  };
  
  $.fn.enterslide        = function( options ) {
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