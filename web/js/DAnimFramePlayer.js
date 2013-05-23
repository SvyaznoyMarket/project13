var DAnimFramePlayer = function (ParentItem) {

	var strBasePath;
	var MainJsonData=null;
	var MainImg=null;
	var Window3dWidth=300;
	var Window3dHeight=300;

	var cSceneState=-1;
	var cSceneFrame;
	var targetRotation=0;
	var targetRotationOnMouseDown;
	var mouseXOnMouseDown = 0;

	var bOldIE=0;
	var requestMain=null;

	var progDAnim=null;

	var MainDiv=document.createElement( 'DIV');
	MainDiv.style.position='relative';
	ParentItem.appendChild(MainDiv);

	var ButtPlay=document.createElement('DIV');
	ButtPlay.style.display='none';
	ButtPlay.innerHTML='<img src="../imgages/icon_play.png" width=80 height=80 border=0>';
	MainDiv.appendChild(ButtPlay);
	var RotateL=document.createElement('DIV');
	RotateL.style.display='none';
	RotateL.innerHTML='<img src="../imgages/rotate_l.png" width=51 height=47 border=0>';
	MainDiv.appendChild(RotateL);
	var RotateR=document.createElement('DIV');
	RotateR.style.display='none';
	RotateR.innerHTML='<img src="../imgages/rotate_r.png" width=55 height=47 border=0>';
	MainDiv.appendChild(RotateR);


	var ImgUrl4Preload=[];
	function AddToPreload(iurl)
	{
		for(var i=0;i<ImgUrl4Preload.length;i++)
			if (ImgUrl4Preload[i]==iurl)
				return;
		ImgUrl4Preload[ImgUrl4Preload.length]=iurl;
	}

    var DoLoadModel=function (strMainJson)
	{
		if (navigator.userAgent.indexOf('MSIE')>0)
		{
			var arr=navigator.userAgent.match(/MSIE ([\d\.]+)/);
			if (arr && (parseFloat(arr[1])<9))
				bOldIE=1;
		}

		if (!bOldIE)
		{
			MainDiv.onselectstart=_onFinishStart;
			MainDiv.addEventListener( 'mousedown', onMainDivMouseDown, false );
			MainDiv.addEventListener( 'touchstart', onDocumentTouchStart, false );
			MainDiv.addEventListener( 'touchmove', onDocumentTouchMove, false );
		}
		else
		{
			MainDiv.onselectstart=_onFinishStart;
			MainDiv.onmousedown=onMainDivMouseDown;
		}
		MainDiv.oncontextmenu=_onFinishStart;
		MainJsonData = strMainJson;
		strBasePath = '';
		onMainData();
	}

	function FinalImgUrl(iurl)
	{
		return IsRelativePath(iurl)?(strBasePath+iurl):iurl;

	}

	function IsRelativePath(path)
	{
		return (path.length<7)||(path.substring(0,7)!='http://');
	}

	function CheckForImgPreload()
	{
		if ((ImgUrl4Preload.length) && (cSceneState==-1))
		{
			var nLoaded=0;
			for(var k=0;k<ImgUrl4Preload.length;k++)
			{
				if ((!bOldIE && ImgUrl4Preload[k].complete) || (bOldIE && (ImgUrl4Preload[k].readyState=='complete')))
					nLoaded++;
			}

    		var percentComplete = 1.*nLoaded / ImgUrl4Preload.length;
			if (progDAnim)
				progDAnim.doDraw(nLoaded*progDAnim.width/ImgUrl4Preload.length)
			if (nLoaded>=ImgUrl4Preload.length)
				OnAfterImageLoad();
		}
	}

	function OnAfterImageLoad()
	{
		if (progDAnim)
			progDAnim.Uninitialize();
		cSceneState=0;
		if (MainJsonData.animation && MainJsonData.animation.length)
		{
			ButtPlay.draggable=false;
			ButtPlay.style.position='absolute';
			ButtPlay.style.cursor='pointer';
			ButtPlay.ontouchstart=startAnimation;
			ButtPlay.onclick=startAnimation;

			ButtPlay.style.left=(Window3dWidth-90)+'px';
			ButtPlay.style.top=(Window3dHeight-90)+'px';
			ButtPlay.style.display='block';

			MainImg.src=FinalImgUrl(MainJsonData.animation[0].img);
		}
		if (MainJsonData.view360 && MainJsonData.view360.length)
		{
			RotateL.style.position='absolute';
			RotateL.style.left='10px';
			RotateL.style.top=(Window3dHeight-50)+'px';
			RotateL.style.display='block';
			RotateL.style.cursor='pointer';
			RotateL.onmousedown=OnPressSmallButtL;
			RotateL.onmouseup=OnReleaseSmallButt;

			RotateR.style.position='absolute';
			RotateR.style.left=(Window3dWidth-140)+'px';
			RotateR.style.top=(Window3dHeight-50)+'px';
			RotateR.style.display='block';
			RotateR.style.cursor='pointer';
			RotateR.onmousedown=OnPressSmallButtR;
			RotateR.onmouseup=OnReleaseSmallButt;
		}
	}

	function startAnimation()
	{
		if ((cSceneState==0) && MainJsonData.animation && MainJsonData.animation.length)
		{	
			ButtPlay.style.display='none';
			if (MainJsonData.view360 && MainJsonData.view360.length)
			{
				RotateL.style.display='none';
				RotateR.style.display='none';
			}

			cSceneState=1;
			
			if (!MainJsonData.view360 || (MainJsonData.view360.length==0) || (Math.floor(0.5+NormAngle(targetRotation)/(2*3.1415926/MainJsonData.view360.length))%MainJsonData.view360.length==0))
				cSceneFrame=0;
			else
				cSceneFrame=-1;
			nextFrame();
		}
	}

	function nextFrame()
	{
		if (cSceneFrame==-1)
		{
			var ii=Math.floor(0.5+NormAngle(targetRotation)/(2*3.1415926/MainJsonData.view360.length));
			if (ii<0.5*MainJsonData.view360.length)
				ii--;
			else
				ii++;
			if (ii%MainJsonData.view360.length==0)
			{
				MainImg.src=FinalImgUrl(MainJsonData.animation[0].img);
				cSceneFrame=0;
			}
			else
			{
				targetRotation=ii*(2*3.1415926/MainJsonData.view360.length);
				ShowRotation();
			}

			setTimeout(nextFrame,30);
		}
		else
		{
			cSceneFrame++;
			if (cSceneFrame<MainJsonData.animation.length)
			{
				MainImg.src=FinalImgUrl(MainJsonData.animation[cSceneFrame].img);
				setTimeout(nextFrame,MainJsonData.animation[cSceneFrame].interval);
			}
			else
			{
				cSceneState=0;
				MainImg.src=FinalImgUrl(MainJsonData.animation[0].img);
				ButtPlay.style.display='block';
				if (MainJsonData.view360 && MainJsonData.view360.length)
				{
					RotateL.style.display='block';
					RotateR.style.display='block';
				}
				targetRotation=0;
			}
		}
	}


	function onMainData() {
		if (MainJsonData)
		{			
			Window3dWidth=MainJsonData.width;
			Window3dHeight=MainJsonData.height;

			MainImg = document.createElement( 'img' );
			MainImg.width=MainJsonData.width;
			MainImg.height=MainJsonData.height;
			if (MainJsonData.splash)
				MainImg.src=FinalImgUrl(MainJsonData.splash);
			if (bOldIE)
				MainImg.ondragStart=stopStart;
			else
				MainImg.addEventListener( 'dragStart', stopStart, false );
			MainImg.draggable=false;
			MainDiv.appendChild( MainImg );
			if (MainJsonData.view360)
			{
				for(var k=0;k<MainJsonData.view360.length;k++)
					AddToPreload(FinalImgUrl(MainJsonData.view360[k]));
			}
			if (MainJsonData.animation)
			{
				for(var k=0;k<MainJsonData.animation.length;k++)
					AddToPreload(FinalImgUrl(MainJsonData.animation[k].img));
			}
			if (ImgUrl4Preload.length>0)
			{
				progDAnim=new DAnimProgressBar(bOldIE);
				progDAnim.Initialize(MainDiv,Window3dWidth,Window3dHeight);
				for(var k=0;k<ImgUrl4Preload.length;k++)
				{
					var iurl=ImgUrl4Preload[k];
					ImgUrl4Preload[k]=new Image();
					if (!bOldIE)
						ImgUrl4Preload[k].onload=CheckForImgPreload;
					else
						ImgUrl4Preload[k].onreadystatechange=CheckForImgPreload;
					ImgUrl4Preload[k].src=iurl;
				}
			}
		}
	}

	function stopStart(event)
	{
		event.preventDefault();
		event.stopPropagation();
	}

	function _onFinishStart()
	{
		return false;
	}


	var playTimeInterval=null;

	function OnPressSmallButtL()
	{
		if (playTimeInterval)
		{
			clearInterval(playTimeInterval);
			playTimeInterval=null;
		}

		ProcessPressActionL();
		playTimeInterval = setInterval(ProcessPressActionL,30);
	}

	function OnPressSmallButtR()
	{
		if (playTimeInterval)
		{
			clearInterval(playTimeInterval);
			playTimeInterval=null;
		}

		ProcessPressActionR();
		playTimeInterval = setInterval(ProcessPressActionR,30);
	}

	function NormAngle(ang)
	{
		while((ang<0) || (ang>=2*3.1415927))
		{
			if (ang>0)
				ang-=2*3.1415926;
			else
				ang+=2*3.1415926;
		}
		return ang;
	}

	function ShowRotation()
	{
		if (MainJsonData.view360 && MainJsonData.view360.length)
		{
			var ii=Math.floor(0.5+NormAngle(targetRotation)/(2*3.1415926/MainJsonData.view360.length));
			MainImg.src=FinalImgUrl(MainJsonData.view360[ii%MainJsonData.view360.length]);
		}
	}

	function ProcessPressActionL()
	{
		targetRotation-= (10) * 0.02;
		ShowRotation();
	}
	function ProcessPressActionR()
	{
		targetRotation+= (10) * 0.02;
		ShowRotation();
	}

	function OnReleaseSmallButt()
	{
		if (playTimeInterval!=null)
		{
			clearInterval(playTimeInterval);
			playTimeInterval=null;
		}
	}

	function onMainDivMouseDown( event ) 
	{
		if (cSceneState==0)
		{
			var clientX=(bOldIE?window.event.offsetX:event.clientX)-MainDiv.offsetLeft;
			 if ((bOldIE?window.event.button:event.button)==2)
			 {
				if (bOldIE)
				{
					window.event.returnValue=false;
					window.event.cancelBubble=true;
				}
				else
					event.preventDefault();
				if (MainJsonData.view360 && MainJsonData.view360.length)
				{
					if (bOldIE)
					{
						MainDiv.onmouseup=onMainDivMouseUp;
						MainDiv.onmousemove=onMainDivMouseMove;
						MainDiv.setCapture();
					}
					else
					{
						document.addEventListener( 'mouseup', onMainDivMouseUp, true );
						document.addEventListener( 'mousemove', onMainDivMouseMove, true );
					}


					mouseXOnMouseDown = clientX - Window3dWidth/2;
					targetRotationOnMouseDown = targetRotation;
				}
			}
		}
	}

	function onMainDivMouseMove( event ) {

		var clientX=(bOldIE?window.event.offsetX:event.clientX)-MainDiv.offsetLeft;

		var mouseX = clientX - Window3dWidth/2;
		targetRotation = targetRotationOnMouseDown + ( mouseX - mouseXOnMouseDown ) * 0.02;
		ShowRotation();
		if (!bOldIE)
			event.stopPropagation();
	}

	function onDocumentContextMenu()
	{
		event.preventDefault();
	}

	function onMainDivMouseUp( event ) 
	{
		if ((bOldIE?window.event.button:event.button)==2)
		{
			if (bOldIE)
			{
				window.event.returnValue=false;
				window.event.cancelBubble=true;
			}
			else
				event.preventDefault();

			if (bOldIE)
			{
				MainDiv.releaseCapture();
				MainDiv.onmouseup=null;
				MainDiv.onmousemove=null;
			}
			else
			{
				document.removeEventListener( 'mouseup', onMainDivMouseUp, true );
				document.removeEventListener( 'mousemove', onMainDivMouseMove, true );
			}
		}
	}

	function onDocumentTouchStart( event ) {
		if (cSceneState==0)
		{
			if ( event.touches.length == 1 ) 
			{
				if (bOldDraw)
				{
					window.event.returnValue=false;
					window.event.cancelBubble=true;
				}
				else
					event.preventDefault();

				mouseXOnMouseDown = event.touches[ 0 ].pageX - Window3dWidth/2;
				targetRotationOnMouseDown = NormAngle(targetRotation);
			}
		}
	}

	function onDocumentTouchMove( event ) {

		if (cSceneState==0)
		{
			if ( event.touches.length == 1 ) 
			{
				event.preventDefault();

				var mouseX = event.touches[ 0 ].pageX - Window3dWidth/2;
				targetRotation = targetRotationOnMouseDown + ( mouseX - mouseXOnMouseDown ) * 0.05;
				ShowRotation();
			}
		}
	}
	return {
		DoLoadModel:DoLoadModel
	}
}

var DAnimProgressBar = function (bOldDrawMode) {

    var ii = 0;
    var res = 0;
    var progressCanvas = null;
    var progressContext = null;
    var progress_total_width = 300;
    var progress_total_height = 34;
    var progress_initial_x = 20;
    var progress_initial_y = 20;
    var progress_radius = progress_total_height/2;
	var bOldDraw=bOldDrawMode;

	var UninitializeProgress=function () {
		progressCanvas.style.display='none';
	}

	var InitializeProgress=function (ParentItem,ParentWidth,ParentHeight) {

		progressCanvas = document.createElement( bOldDraw?'DIV':'canvas' );
		if (!bOldDraw)
		{
			progressCanvas.width=500;
			progressCanvas.height=150;
		}
		progressCanvas.style.position = 'absolute';
		progressCanvas.style.left = Math.floor((ParentWidth-progress_total_width)/2)+'px';
		progressCanvas.style.top = Math.floor((ParentHeight-progress_total_height)/2)+'px';
		ParentItem.appendChild( progressCanvas );

		if (bOldDraw)
		{
			progressCanvas.style.width=progress_total_width+'px';
			progressCanvas.style.height=(progress_total_height/2)+'px';
			progressCanvas.style.overflow='hidden';
			progressCanvas.style.borderColor='#000000';
			progressCanvas.style.borderWidth='1px';
			progressCanvas.style.borderStyle='solid';
			progressCanvas.style.backgroundColor='#FFFFFF';

			progressCanvas.innerHTML='<DIV style="background-color:#ADD9FF;width:0px;height:'+(progress_total_height/2)+'px;"></DIV>';

		}



		if (!bOldDraw)
		{
            if (!progressCanvas || !progressCanvas.getContext)
                return;
            progressContext = progressCanvas.getContext('2d');
            if (!progressContext)
                return;

            // set font
            progressContext.font = "16px Verdana";

            // Blue gradient for progress bar
            var progress_lingrad = progressContext.createLinearGradient(0,progress_initial_y+progress_total_height,0,0);
            progress_lingrad.addColorStop(0, '#4DA4F3');
            progress_lingrad.addColorStop(0.4, '#ADD9FF');
            progress_lingrad.addColorStop(1, '#9ED1FF');
            progressContext.fillStyle = progress_lingrad;
		}

		doDrawProgress(0);
    }

    var doDrawProgress=function (iPercent) {
		if (!bOldDraw)
		{
            // Clear everything before drawing
            progressContext.clearRect(progress_initial_x-5,progress_initial_y-5,progress_total_width+15,progress_total_height+15);
            progressLayerRect(progressContext, progress_initial_x, progress_initial_y, progress_total_width, progress_total_height, progress_radius);
            progressBarRect(progressContext, progress_initial_x, progress_initial_y, iPercent, progress_total_height, progress_radius, progress_total_width);
            progressText(progressContext, progress_initial_x, progress_initial_y, iPercent, progress_total_height, progress_radius, progress_total_width );
		}
		else
		{
			var pc=progressCanvas.children(0);
			if (pc)
				pc.style.width=iPercent+'px';
		}
    }

    function roundRect(ctx, x, y, width, height, progress_radius) {
        ctx.beginPath();
        ctx.moveTo(x + progress_radius, y);
        ctx.lineTo(x + width - progress_radius, y);
        ctx.arc(x+width-progress_radius, y+progress_radius, progress_radius, -Math.PI/2, Math.PI/2, false);
        ctx.lineTo(x + progress_radius, y + height);
        ctx.arc(x+progress_radius, y+progress_radius, progress_radius, Math.PI/2, 3*Math.PI/2, false);
        ctx.closePath();
        ctx.fill();
    }

    function roundInsetRect(ctx, x, y, width, height, progress_radius) {
        ctx.beginPath();
        // Draw huge anti-clockwise box
        ctx.moveTo(1000, 1000);
        ctx.lineTo(1000, -1000);
        ctx.lineTo(-1000, -1000);
        ctx.lineTo(-1000, 1000);
        ctx.lineTo(1000, 1000);
        ctx.moveTo(x + progress_radius, y);
        ctx.lineTo(x + width - progress_radius, y);
        ctx.arc(x+width-progress_radius, y+progress_radius, progress_radius, -Math.PI/2, Math.PI/2, false);
        ctx.lineTo(x + progress_radius, y + height);
        ctx.arc(x+progress_radius, y+progress_radius, progress_radius, Math.PI/2, 3*Math.PI/2, false);
        ctx.closePath();
        ctx.fill();
    }

    function progressLayerRect(ctx, x, y, width, height, progress_radius) {
        ctx.save();
        // Set shadows to make some depth
        ctx.shadowOffsetX = 2;
        ctx.shadowOffsetY = 2;
        ctx.shadowBlur = 5;
        ctx.shadowColor = '#666';

         // Create initial grey layer
        ctx.fillStyle = 'rgba(189,189,189,1)';
        roundRect(ctx, x, y, width, height, progress_radius);

        // Overlay with gradient
        ctx.shadowColor = 'rgba(0,0,0,0)'
        var lingrad = ctx.createLinearGradient(0,y+height,0,0);
        lingrad.addColorStop(0, 'rgba(255,255,255, 0.1)');
        lingrad.addColorStop(0.4, 'rgba(255,255,255, 0.7)');
        lingrad.addColorStop(1, 'rgba(255,255,255,0.4)');
        ctx.fillStyle = lingrad;
        roundRect(ctx, x, y, width, height, progress_radius);

        ctx.fillStyle = 'white';
        //roundInsetRect(ctx, x, y, width, height, progress_radius);

        ctx.restore();
    }

    function progressBarRect(ctx, x, y, width, height, progress_radius, max) {
        // var to store offset for proper filling when inside rounded area
        var offset = 0;
        ctx.beginPath();
        if (width<progress_radius) {
            offset = progress_radius - Math.sqrt(Math.pow(progress_radius,2)-Math.pow((progress_radius-width),2));
            ctx.moveTo(x + width, y+offset);
            ctx.lineTo(x + width, y+height-offset);
            ctx.arc(x + progress_radius, y + progress_radius, progress_radius, Math.PI - Math.acos((progress_radius - width) / progress_radius), Math.PI + Math.acos((progress_radius - width) / progress_radius), false);
        }
        else if (width+progress_radius>max) {
            offset = progress_radius - Math.sqrt(Math.pow(progress_radius,2)-Math.pow((progress_radius - (max-width)),2));
            ctx.moveTo(x + progress_radius, y);
            ctx.lineTo(x + width, y);
            ctx.arc(x+max-progress_radius, y + progress_radius, progress_radius, -Math.PI/2, -Math.acos((progress_radius - (max-width)) / progress_radius), false);
            ctx.lineTo(x + width, y+height-offset);
            ctx.arc(x+max-progress_radius, y + progress_radius, progress_radius, Math.acos((progress_radius - (max-width)) / progress_radius), Math.PI/2, false);
            ctx.lineTo(x + progress_radius, y + height);
            ctx.arc(x+progress_radius, y+progress_radius, progress_radius, Math.PI/2, 3*Math.PI/2, false);
        }
        else {
            ctx.moveTo(x + progress_radius, y);
            ctx.lineTo(x + width, y);
            ctx.lineTo(x + width, y + height);
            ctx.lineTo(x + progress_radius, y + height);
            ctx.arc(x+progress_radius, y+progress_radius, progress_radius, Math.PI/2, 3*Math.PI/2, false);
        }
        ctx.closePath();
        ctx.fill();

        // draw progress bar right border shadow
        if (width<max-1) {
            ctx.save();
            ctx.shadowOffsetX = 1;
            ctx.shadowBlur = 1;
            ctx.shadowColor = '#666';
            if (width+progress_radius>max)
              offset = offset+1;
            ctx.fillRect(x+width,y+offset,1,progress_total_height-offset*2);
            ctx.restore();
        }
    }

    function progressText(ctx, x, y, width, height, progress_radius, max) {
        ctx.save();
        ctx.fillStyle = 'white';
        var text = Math.floor(width/max*100)+"%";
        var text_width = ctx.measureText(text).width;
        var text_x = x+width-text_width-progress_radius/2;
        if (width<=progress_radius+text_width) {
            text_x = x+progress_radius/2;
        }
        ctx.fillText(text, text_x, y+22);
        ctx.restore();
    }

	return {
        width: progress_total_width,
        height: progress_total_height,

		Initialize: InitializeProgress,
		Uninitialize: UninitializeProgress,
		doDraw: doDrawProgress

	}
}
