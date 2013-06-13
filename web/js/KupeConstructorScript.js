//#ff9e18 border
var DKupe3dConstructor = function (ParentItem, HostImgs, HostTextures) {

var container, stats;

var mouse2D;
var camera, scene, renderer;
var cameraDistance=15;
var cameraXshift=0;
var stats;

var mouseX = 0, mouseY = 0;
var raycaster, projector;

var Window3dWidth=430;
var Window3dHeight=450;


var windowHalfX = Window3dWidth / 2;
var windowHalfY = Window3dHeight / 2;

var iconSize=50;

var bMouseMoveBetweenClick;
var bMouseNavigationActive=0;
var targetRotation = 0;
var targetRotationOnMouseDown = 0;

var mouseXOnMouseDown = 0;

var MainJsonData=null;

var Korpus3dHolder;
var Lamp3dHolder;
var Doors3dHolders=[];
var Doors3dHoldersMax=3;

var CurrentVstavkaMaterial;
var KupeParams=new Object();
KupeParams.nDoors=0;

var iActiveDoor=0;
var iActiveDoorSection=-1;
KupeParams.doors=[];

var hideTimeout=null;
var cPopupMenu;

var progDAnim=null;
var MSIE=0;

var MainDiv=null;
var PopupMenuDiv=null;
var ButtColorPicker=null;

function Initialize(strJsonFile)
{
	var arr=navigator.userAgent.match(/MSIE ([\d\.]+)/);
	if (arr)
	   MSIE=parseFloat(arr[1]);

	MainDiv=document.createElement('DIV');
	MainDiv.setAttribute('id', 'Planner3d_MainDiv');
	MainDiv.style.position='relative';
	ParentItem.appendChild(MainDiv);
	MainDiv.innerHTML='<div id="Planner3d_tab_info" style="color: #666666;font-family:\'Enter Type Bold\';font-size:18px;position: absolute;top: 0px;left: 50%;width: 200px;position:absolute;z-index: 100;display:none;">\
	<table cellpadding=0 cellspacing=0 width=250>\
	<tr><td height=25 id=Planner3d_TabInfo1Header width=120 align=center>Выбор корпуса</td><td height=25 width=5>&nbsp;</td><td id=Planner3d_TabInfo2Header width=120 align=center>Выбор фасада</td></tr>\
	<tr><td id=Planner3d_KorpusVariantsDiv height=80 colspan=3 style="padding-left:5px;border:1px solid #c1c1bf;"></td></tr>\
	<tr><td id=Planner3d_TabInfo colspan=3 height=183 valign=top style="padding-top:5px;padding-bottom:5px;padding-left:5px;border-left:1px solid #c1c1bf;border-right:1px solid #c1c1bf;border-bottom:1px solid #c1c1bf;">\
	<div id=Planner3d_TabInfo1 style="display:none;">&nbsp;</div><div id=Planner3d_TabInfo2 style="display:none;">\
	<font style="font-family:\'Enter Type\';color:#000000;font-size:13px;">Выбор цвета вставки</font><br>\
	<div id="Planner3d_scrollcontent"></div></div></td></tr>\
	<tr><td id=Planner3d_LampVariantsDiv height=80 colspan=4 style="padding-left:5px;border-left:1px solid #c1c1bf;border-right:1px solid #c1c1bf;border-bottom:1px solid #c1c1bf;"></td></tr>\
	<tr><td id=Planner3d_ProfileVariantsDiv height=80 colspan=4 style="padding-left:5px;border-left:1px solid #c1c1bf;border-right:1px solid #c1c1bf;border-bottom:1px solid #c1c1bf;"></td></tr>\
	</table></div>';


	document.getElementById('Planner3d_TabInfo1Header').addEventListener( 'click', ActivateTab1, false );
	document.getElementById('Planner3d_TabInfo2Header').addEventListener( 'click', ActivateTab2, false );

	PopupMenuDiv=document.createElement('DIV');
	PopupMenuDiv.style.position='absolute';
	PopupMenuDiv.style.zIndex='200';
	PopupMenuDiv.style.display='none';
	PopupMenuDiv.addEventListener( 'mouseover', showPopupMenu, false );
	PopupMenuDiv.addEventListener( 'mouseout', OnPopupOut, false );
	MainDiv.appendChild(PopupMenuDiv);

	ButtColorPicker=document.createElement('DIV');
	ButtColorPicker.style.position='absolute';
	ButtColorPicker.style.cursor='pointer';
	ButtColorPicker.style.display='none';
	ButtColorPicker.innerHTML='<img src="'+HostImgs+'icon_colorpicker.png" width=27 height=26 border=0>';
	ButtColorPicker.addEventListener( 'mousedown', stopStart, false );
	ButtColorPicker.addEventListener( 'click', OnClickSmallButtColorPicker, false );
	MainDiv.appendChild(ButtColorPicker);

	progDAnim=new DAnimProgressBar(0);
	progDAnim.Initialize(MainDiv,Window3dWidth,Window3dHeight);


	var requestMain = new XMLHttpRequest();
	requestMain.addEventListener( 'load', onMainData, false );
	requestMain.addEventListener( 'progress', onMainDataProgress, false );
	requestMain.open( 'GET', strJsonFile, true );
	requestMain.send( null );
}

function Uninitialize() {}


var MainJsonText;
function onMainData( event ) {
	if (progDAnim)
		progDAnim.doDraw(progDAnim.width)
	MainJsonText=event.target.responseText;
	setTimeout(init,1);
}

function onMainDataProgress( event ) {
	if (event.lengthComputable) {
    	var percentComplete = event.loaded / event.total;
		if (progDAnim)
			progDAnim.doDraw(progDAnim.width*event.loaded/event.total)
	} 
	else {
    	// Unable to compute progress information since the total size is unknown
	}			
}

function ArrayHasItem(arr,val)
{
	for(var k=0;k<arr.length;k++)
		if (arr[k]==val)
			return 1;
	return 0;
}

function NormAngle(camAngIni)
{
	var camAng=camAngIni;
	while(Math.abs(camAng)>3.1415927)
	{
		if (camAng>0)
			camAng-=2*3.1415926;
		else
			camAng+=2*3.1415926;
	}
	return camAng;
}

function rgb2hex( rgb ) {
	return ( rgb[ 0 ] * 255 << 16 ) + ( rgb[ 1 ] * 255 << 8 ) + rgb[ 2 ] * 255;
}

var MatLib=new Array();
function LoadMaterialByName(matName)
{
	if (MatLib[matName]!==undefined)
		return MatLib[matName];
	var mat=GetMaterial(matName);
	if (mat)
	{
		var matObj=MyCreateMaterial(mat);
		MatLib[matName]=matObj;
		return matObj;
	}

	var mpars=new Object();
		mpars.color = 0xF0F0F0;
	return new THREE.MeshLambertMaterial(mpars);
}

function MyCreateMaterial(m)
{
	var mpars=new Object();

	if ( m.colorDiffuse && !( m.mapDiffuse && m.mapDiffuse.length))
		mpars.color = rgb2hex( m.colorDiffuse );
	if ( m.colorSpecular )
			mpars.specular = rgb2hex( m.colorSpecular );
	if ( m.colorAmbient )
			mpars.ambient = rgb2hex( m.colorAmbient );
	if ( m.transparency )
			mpars.opacity = m.transparency;
	if ( m.specularCoef )
		mpars.shininess = m.specularCoef;
	if ( m.mapDiffuse )
	{
		mpars.map=THREE.ImageUtils.loadTexture( HostTextures+m.mapDiffuse );
		mpars.map.wrapS = THREE.RepeatWrapping
		mpars.map.wrapT = THREE.RepeatWrapping
		if ( m.mapDiffuseScale )
			mpars.map.repeat.set(m.mapDiffuseScale[0],m.mapDiffuseScale[1]);
		if ( m.mapDiffuseOffset )
			mpars.map.offset.set(m.mapDiffuseOffset[0],m.mapDiffuseOffset[1]);
	}
	if (m.cubeMap)
	{
		var reflectionCube = THREE.ImageUtils.loadTextureCube( m.cubeMap );
		reflectionCube.format = THREE.RGBFormat;

		mpars.envMap=reflectionCube;
		mpars.combine=THREE.MixOperation;
		mpars.reflectivity=0.3;
	}

	return new THREE.MeshPhongMaterial(mpars);
}
	function FindAllArts(AllApiIds,Variants)
	{
		for(var ik=0;ik<Variants.length;ik++)
		{
			var api_id=Variants[ik].api_id;
			if (api_id.length)
			{
				var ii=api_id.indexOf('/');
				if (ii>0)
					api_id=api_id.substring(0,ii);
				if (!ArrayHasItem(AllApiIds,api_id))
					AllApiIds.push(api_id);
			}
		}
	}

	function init() {
		MainJsonData = JSON.parse(MainJsonText);
		if (MainJsonData)
		{
			if (!MSIE || (MSIE>=11))
			{
				var RotateL=document.createElement('DIV');
				RotateL.setAttribute('id', 'RotateL');
				RotateL.innerHTML='<img draggable="false" src=\"'+HostImgs+'rotate_left.png\" width=35 height=43 border=0>';
				MainDiv.appendChild(RotateL);
				RotateL.addEventListener( 'mousedown', OnPressSmallButtRotateL, false );
				RotateL.style.position='absolute';
				RotateL.style.cursor='pointer';

				var RotateC=document.createElement('DIV');
				RotateC.setAttribute('id', 'RotateC');
				RotateC.innerHTML='<img draggable="false" src=\"'+HostImgs+'rotate_center.png\" width=51 height=47 border=0>';
				MainDiv.appendChild(RotateC);
				RotateC.style.position='absolute';

				var RotateR=document.createElement('DIV');
				RotateR.setAttribute('id', 'RotateR');
				RotateR.innerHTML='<img draggable="false" src=\"'+HostImgs+'rotate_right.png\" width=35 height=43 border=0>';
				MainDiv.appendChild(RotateR);
				RotateR.addEventListener( 'mousedown', OnPressSmallButtRotateR, false );
				RotateR.style.position='absolute';
				RotateR.style.cursor='pointer';

				var ZoomOut=document.createElement('DIV');
				ZoomOut.setAttribute('id', 'ZoomOut');
				ZoomOut.innerHTML='<img draggable="false" src=\"'+HostImgs+'zoom_out.png\" width=41 height=39 border=0>';
				MainDiv.appendChild(ZoomOut);
				ZoomOut.addEventListener( 'mousedown', OnPressSmallButtZoomOut, false );
				ZoomOut.style.position='absolute';
				ZoomOut.style.cursor='pointer';

				var ZoomIn=document.createElement('DIV');
				ZoomIn.setAttribute('id', 'ZoomIn');
				ZoomIn.innerHTML='<img draggable="false" src=\"'+HostImgs+'zoom_in.png\" width=41 height=39 border=0>';
				MainDiv.appendChild(ZoomIn);
				ZoomIn.addEventListener( 'mousedown', OnPressSmallButtZoomIn, false );
				ZoomIn.style.position='absolute';
				ZoomIn.style.cursor='pointer';

				RotateL.style.top=(Window3dHeight+10)+'px';
				RotateL.style.left=(Window3dWidth/2-20-35)+'px';
				RotateR.style.top=(Window3dHeight+10)+'px';
				RotateR.style.left=(Window3dWidth/2+33)+'px';
				RotateC.style.left=(Window3dWidth/2-20)+'px';
				RotateC.style.top=(Window3dHeight+10)+'px';

				ZoomOut.style.left='0px';
				ZoomOut.style.top='0px';
				ZoomIn.style.left='0px';
				ZoomIn.style.top='50px';
			}
			if (MainJsonData.articuls)
			{
				var KorpusesArray=[];

				var KorpMap=[];
				for(var k=0;k<MainJsonData.korpuses.length;k++)
				{
					for(var ii=0;ii<MainJsonData.articuls.korpuses.length;ii++)
					{
						if (MainJsonData.korpuses[k].name==MainJsonData.articuls.korpuses[ii].name)
						{
							KorpusesArray.push(MainJsonData.korpuses[k]);
							break;
						}
					}
				}
				MainJsonData.korpuses=KorpusesArray;
			}

			MainJsonText=undefined;
			if (progDAnim)
				progDAnim.Uninitialize();

			container = document.createElement( 'div' );
			MainDiv.appendChild( container );

			document.body.addEventListener( 'drop', onDropHandler, false );
			document.body.addEventListener( 'dragenter', onDragEnter, false );
			document.body.addEventListener( 'dragover', onDragOver, false );

			camera = new THREE.PerspectiveCamera( 10, Window3dWidth / Window3dHeight, 1, 2000 );
			camera.position.x = 0;
			camera.position.y = 1.2;
			camera.position.z = cameraDistance;

			// scene

			scene = new THREE.Scene();

			var ambient = new THREE.AmbientLight( 0x4C4C4C );
			scene.add( ambient );

			var directionalLight1 = new THREE.DirectionalLight( 0x999999 );
			directionalLight1.position.set( 1, 0, 0 ).normalize();
			scene.add( directionalLight1);

			var directionalLight2 = new THREE.DirectionalLight( 0x7F7F7F );
			directionalLight2.position.set( -1, 0, 0 ).normalize();
			scene.add( directionalLight2);

			var directionalLight3 = new THREE.DirectionalLight( 0x595959 );
			directionalLight3.position.set( 0, 1, 0 ).normalize();
			scene.add( directionalLight3);

			var directionalLight4 = new THREE.DirectionalLight( 0x727272 );
			directionalLight4.position.set( 0, -1, 0 ).normalize();
			scene.add( directionalLight4);

			var directionalLight5 = new THREE.DirectionalLight( 0x999999 );
			directionalLight5.position.set( 0, 0, 1 ).normalize();
			scene.add( directionalLight5);

			var directionalLight6 = new THREE.DirectionalLight( 0x999999 );
			directionalLight6.position.set( 0, 0, -1 ).normalize();
			scene.add( directionalLight6);

			var floorgeo = new THREE.CubeGeometry(600,600,5);
			floormesh = new THREE.Mesh(floorgeo, new THREE.MeshBasicMaterial({color: 0xCCCCCC}));
			floormesh.position.y = -2.5;
			floormesh.rotation.x = 90 * Math.PI / 180;
			//scene.add(floormesh);

			Korpus3dHolder=new THREE.Object3D();
			scene.add( Korpus3dHolder );
			Lamp3dHolder=new THREE.Object3D();
			scene.add( Lamp3dHolder );
			for(var k=0;k<Doors3dHoldersMax;k++)
			{
				var Doors3dHolder=new THREE.Object3D();
				Doors3dHolders[k]=Doors3dHolder;
				scene.add( Doors3dHolder );
			}

			for(var k=0;k<Doors3dHoldersMax;k++)
			{
				KupeParams.doors[k]=new Object();
				KupeParams.doors[k].materials=['','','',''];
			}


			// texture

			projector = new THREE.Projector();
			mouse2D = new THREE.Vector3( 0, 10000, 0.5 );

			loader = new THREE.JSONLoader();
			if (KupeParams.KorpusName==undefined)
				KupeParams.KorpusName=MainJsonData.korpuses[0].name;
			if (KupeParams.KorpusMaterial==undefined)
				KupeParams.KorpusMaterial=GetKorpusPossibleMaterials(KupeParams.KorpusName)[0];
			if (KupeParams.ProfileMaterial==undefined)
				KupeParams.ProfileMaterial=GetProfilePossibleMaterials(KupeParams.KorpusName)[0];
			if (KupeParams.Lamp==undefined)
				KupeParams.Lamp=0;

			CreateFullKupe();

			UpdateKorpusesDiv();

			var tab=document.getElementById('Planner3d_tab_info');
			tab.style.display='block';
			tab.style.left=(Window3dWidth+20)+'px';
			tab.style.height=Window3dHeight+'px';
			ActivateTab(1);
			//
			if (!MSIE || (MSIE>=11))
				renderer = new THREE.WebGLRenderer({ antialias: true });
			else
				renderer = new THREE.CanvasRenderer();
			renderer.setSize( Window3dWidth, Window3dHeight );

			container.appendChild( renderer.domElement );
			renderer.domElement.style.borderBottom='2px solid #c1c1bf'

			if (0)
			{
				stats = new Stats();
				stats.domElement.style.position = 'absolute';
				stats.domElement.style.top = '0px';
				container.appendChild( stats.domElement );
			}
			else
				stats=null;

			MainDiv.addEventListener( 'mousewheel', onMouseWheel, false );

			document.addEventListener( 'mousedown', onDocumentMouseDown, false );
			document.addEventListener( 'touchstart', onDocumentTouchStart, false );
			document.addEventListener( 'touchmove', onDocumentTouchMove, false );
			document.addEventListener( 'mousemove', onDocumentMouseMove, false );

			//document.body.addEventListener( 'selectStart', stopStart, false );
			document.onselectstart=_onFinishStart;

			//document.addEventListener( '', onDocumentContextMenu, false );
			document.oncontextmenu=_onFinishStart;

			//document.on=null;
			animate();

			if (MainJsonData.articuls)
			{
				var AllApiIds=[];
				for(var ik=0;ik<MainJsonData.articuls.korpuses.length;ik++)
					FindAllArts(AllApiIds,MainJsonData.articuls.korpuses[ik].variants);
				for(var iv=0;iv<MainJsonData.articuls.vstavki.length;iv++)
					FindAllArts(AllApiIds,MainJsonData.articuls.vstavki[iv].variants);
				for(var ip=0;ip<MainJsonData.articuls.profiles.length;ip++)
					FindAllArts(AllApiIds,MainJsonData.articuls.profiles[ip].variants);
				for(var ii=0;ii<MainJsonData.articuls.doors.length;ii++)
					for(var jj=0;jj<MainJsonData.articuls.doors[ii].types.length;jj++)
						FindAllArts(AllApiIds,MainJsonData.articuls.doors[ii].types[jj].variants);


				Planner3d_Init(AllApiIds);
				UpdatePrice();
			}
		}
	}

	function AppendSceneApiIdsIds(SceneApiIds,Variants,ColorName)
	{
		for(var ik=0;ik<Variants.length;ik++)
		{
			if (Variants[ik].color==ColorName)
			{
				var api_id=Variants[ik].api_id;
				if (api_id.length)
				{
					var ii=api_id.indexOf('/');
					if (ii>0)
					{
						if (api_id.substring(ii)=='/2')
						{
							for(var k=0;k<SceneApiIds.length;k++)
							{
								if (SceneApiIds[k]==api_id)
								{
									SceneApiIds[k]=api_id.substring(0,ii);
									return;
								}
							}
						}
						else
						{/* not impl */}
					}

					SceneApiIds.push(api_id);
				}
			}
		}
	}

	function UpdatePrice()
	{
		var SceneApiIds=[];

		for(var ii=0;ii<MainJsonData.articuls.korpuses.length;ii++)
		{
			if (MainJsonData.articuls.korpuses[ii].name==KupeParams.KorpusName)
			{
				AppendSceneApiIdsIds(SceneApiIds,MainJsonData.articuls.korpuses[ii].variants,KupeParams.KorpusMaterial);
				break;
			}
		}
		var Korpus=GetKorpusByName(KupeParams.KorpusName);
		if (Korpus)
		{
			for(var ii=0;ii<MainJsonData.articuls.profiles.length;ii++)
			{
				if (Korpus.width==MainJsonData.articuls.profiles[ii].width)
				{
					AppendSceneApiIdsIds(SceneApiIds,MainJsonData.articuls.korpuses[ii].variants,KupeParams.ProfileMaterial);
					break;
				}
			}

			for(var ii=0;ii<MainJsonData.articuls.doors.length;ii++)
			{
				if (MainJsonData.articuls.doors[ii].name==Korpus.doors.type)
				{
					for(var n=0;n<KupeParams.nDoors;n++)
					{
						for(var jj=0;jj<MainJsonData.articuls.doors[ii].types.length;jj++)
						{
							if (MainJsonData.articuls.doors[ii].types[jj].type==KupeParams.doors[n].variantType)
							{
								AppendSceneApiIdsIds(SceneApiIds,MainJsonData.articuls.doors[ii].types[jj].variants,KupeParams.ProfileMaterial);
								break;
							}
						}
					}
					break;
				}
			}

			var Door=GetDoorByType(KupeParams.DoorsType)
			if (Door)
			{
				for(var n=0;n<KupeParams.nDoors;n++)
				{
					var Sections=null;
					for(var k=0;(k<Door.variants.length) && (Sections==null);k++)
						if (Door.variants[k].type==KupeParams.doors[n].variantType)
							Sections=Door.variants[k].sections;
					if (Sections)
					{
						for(var iSection=0;iSection<KupeParams.doors[n].sections.length;iSection++)
						{
							for(var iv=0;iv<MainJsonData.articuls.vstavki.length;iv++)
							{
								if ((MainJsonData.articuls.vstavki[iv].width==Door.width) && (MainJsonData.articuls.vstavki[iv].height==Sections[iSection].height))
								{
									AppendSceneApiIdsIds(SceneApiIds,MainJsonData.articuls.vstavki[iv].variants,KupeParams.doors[n].materials[iSection]);
									break;
								}
							}
						}
					}
				}
			}
		}

		var out=[];
		for(var k=0;k<SceneApiIds.length;k++)
		{
			api_id=SceneApiIds[k];
			var ii=api_id.indexOf('/');
			if (ii>0)
				out.push({'id':parseInt(api_id.substring(0,ii)), error:'Вставки продаютcя только комплектом по 2шт!'});
			else
				out.push({'id':parseInt(api_id), error:''});
		}
		Planner3d_UpdatePrice(out);
	}

	function OnShapeLoaded(geomType,geomIndex,geometry,materials)
	{
		var Mesh = new THREE.Mesh( geometry, new THREE.MeshFaceMaterial( materials ) );
		if (geomType=='Korpus')
			Korpus3dHolder.add( Mesh );
		else if (geomType=='Lamp')
		{
			for(var k=0;k<Lamp3dHolder.children.length;k++)
			{
				if (!KupeParams.Lamp)
					Mesh.visible=false;
				Lamp3dHolder.children[k].add( (k==0)?Mesh:Mesh.clone() );
			}
		}
		else
		{
			for(var k=0;k<Doors3dHoldersMax;k++)
			{
				if (geomType=='Door'+k)
				{
					if (iActiveTab==1)
						Mesh.visible=false;

					Doors3dHolders[k].add( Mesh );
					return;
				}
			}
		}
	}

	var iActiveTab=1;
	function ActivateTab1() { ActivateTab(1);}
	function ActivateTab2() { ActivateTab(2);}
	function ActivateTab(iTab)			   
	{
		iActiveTab=iTab;
		DoActivateTab('TabInfo'+iTab,'TabInfo'+(3-iTab));
		for(var i=0;i<KupeParams.nDoors;i++)
		{
			for(var k=0;k<Doors3dHolders[i].children.length;k++)
				Doors3dHolders[i].children[k].visible=(iActiveTab==1)?false:true;
		}
		if (iActiveTab==1)
			ButtColorPicker.style.display='none';

		document.getElementById('Planner3d_KorpusVariantsDiv').height=(iActiveTab==1)?90:57;
		document.getElementById('Planner3d_TabInfo').height=(iActiveTab==1)?160:195;

		var syn='';
		if (iActiveTab==1)
		{
			syn='<font style="font-family:\'Enter Type\';color:#000000;font-size:13px;">Выбор цвета корпуса</font><br>';
			syn+='<table cellpadding=0 cellspacing=0 onclick="Planner3dKupeConstructor.SetKorpusMaterial(\'\',event)">';
			syn+='<tr>';

			var colors=GetKorpusPossibleMaterials(KupeParams.KorpusName);
			for(var k=0;k<colors.length;k++)
			{
				var mat=GetMaterial(colors[k]);
				if (mat)
				{
					syn+='<td onmouseover="Planner3dKupeConstructor.showPopupMenu(\'mat_'+colors[k]+'\')" onmouseout="Planner3dKupeConstructor.OnPopupOut()" width='+(iconSize+4)+' height='+(iconSize+4)+' valign=middle align=center style="';
					syn+='border:2px solid '+((KupeParams.KorpusMaterial==colors[k])?'#ff9e18;':'#ffffff;');
					syn+='">';
					syn+='<div id="mat_'+colors[k]+'" style="width:'+iconSize+'px;height:'+iconSize+'px;background-image:url('+mat.icon+');cursor:pointer;" title="'+mat.description+'"></div>';
					syn+='</td>';
				}
			}
			syn+='</tr>';
			syn+='</table>';
		}

		if (iActiveTab==2)
		{
			syn+='<table cellpadding=0 cellspacing=0 onclick="Planner3dKupeConstructor.SetCurrentVstavkaMaterial(\'\',event)">';
			syn+='<tr>';

			var colors=GetVstavkaPossibleMaterials(KupeParams.DoorsType);
			if (!ArrayHasItem(colors,CurrentVstavkaMaterial))
				CurrentVstavkaMaterial=colors[0];
			for(var k=0;k<colors.length;k++)
			{
				if ((k>0)&&((k%4)==0))
					syn+='</tr><tr>';
					
					
				var mat=GetMaterial(colors[k]);
				if (mat)
				{
					syn+='<td onmouseover="Planner3dKupeConstructor.showPopupMenu(\'mat_'+colors[k]+'\')" onmouseout="Planner3dKupeConstructor.OnPopupOut()" width='+(iconSize+4)+' height='+(iconSize+4)+' valign=middle align=center style="';
					syn+='border:2px solid '+((CurrentVstavkaMaterial==colors[k])?'#ff9e18;':'#ffffff;');
					syn+='">';
					syn+='<div draggable="true" id="mat_'+colors[k]+'" style="width:'+iconSize+'px;height:'+iconSize+'px;background-image:url('+mat.icon+');cursor:pointer;" ondragstart="Planner3dKupeConstructor.onDragMatStart(\''+colors[k]+'\', event)" ondragend="Planner3dKupeConstructor.onDragMatEnd()"  title="'+mat.description+'"></div>';
					syn+='</td>';
				}
			}
			syn+='</tr>';
			syn+='</table>';
		}

		if (iActiveTab==1)
			document.getElementById('Planner3d_TabInfo'+iActiveTab).innerHTML=syn;
		else
			document.getElementById('Planner3d_scrollcontent').innerHTML=syn;
			

		if (iActiveTab==1)
			syn='<font style="font-family:\'Enter Type\';color:#000000;font-size:13px;">Выбор цвета профиля</font><br>';
		else
			syn='<font style="font-family:\'Enter Type\';color:#000000;font-size:13px;">Выбор цвета рамки</font><br>';
		syn+='<table cellpadding=0 cellspacing=0>';
		syn+='<tr>';

		var colors=GetProfilePossibleMaterials(KupeParams.KorpusName);
		for(var k=0;k<colors.length;k++)
		{
			var iconW=50,iconH=42;
			var mat=GetMaterial(colors[k]);
			if (mat)
			{
				syn+='<td width='+(iconW+6)+' height='+(iconH+6)+' valign=middle align=center style="';
				if (KupeParams.ProfileMaterial==colors[k])
					syn+='border:2px solid #ff9e18;';
				else
					syn+='border:2px solid #ffffff;';
				syn+='">';
				syn+='<img ondragstart="Planner3dKupeConstructor.stopStart(event)" onclick="Planner3dKupeConstructor.SetProfileMaterial(\''+colors[k]+'\',event)" width='+iconW+' height='+iconH+' src="'+mat.icon+'" title="'+mat.description+'">';
				syn+='</td>';
			}
		}
		syn+='</tr>';
		syn+='</table>';
		document.getElementById('Planner3d_ProfileVariantsDiv').innerHTML=syn;

		var CurrentKorpus=GetKorpusByName(KupeParams.KorpusName);


		if (MainJsonData.articuls.lamps)
		{
			syn='<font style="font-family:\'Enter Type\';color:#000000;font-size:13px;">Подсветка</font><br>';
			syn+='<table cellpadding=0 cellspacing=0>';
			syn+='<tr>';

					syn+='<td valign=middle align=center style="';
					if (KupeParams.Lamp)
						syn+='border:2px solid #ff9e18;';
					else
						syn+='border:2px solid #ffffff;';
					syn+='">';
					syn+='<img ondragstart="Planner3dKupeConstructor.stopStart(event)" onclick="Planner3dKupeConstructor.SwitchLamp(event)" width='+iconW+' height='+iconH+' src="'+HostImgs+'lamp.png">';
					syn+='</td>';
			syn+='</tr>';
			syn+='</table>';
			document.getElementById('Planner3d_LampVariantsDiv').innerHTML=syn;
		}

		UpdateKorpusesDiv();


	}

	function UnborderTableCells(tbl,cellSel)
	{
		var rows=tbl.rows;
		for(var i=0;i<rows.length;i++)
		{
			var cells=rows.item(i).cells;
			for(var j=0;j<cells.length;j++)
			{
				var cell=cells.item(j);
				if (cell && (cell!=cellSel))
					cell.style.borderColor='#FFFFFF';
			}
		}
	}

	function SetKorpusMaterial(matName,event)
	{
		if (SetKorpusMaterial.arguments.length>=2)
		{
			if ((event.target.tagName=='DIV')&&(event.target.id.substring(0,4)=='mat_'))
				matName=event.target.id.substring(4);
			else
				return;
		}

		var mat=GetMaterial(matName);
		if (mat)
		{
			Korpus3dHolder.children[0].material=LoadMaterialByName(matName);
			KupeParams.KorpusMaterial=matName;

			if (SetKorpusMaterial.arguments.length>=2)
			{
				var td=event.target?event.target.offsetParent:null;
				if (td.tagName=='TD')
				{
					UnborderTableCells(td.offsetParent,td)
					td.style.borderColor='#ff9e18';
				}
			}
		}
		if (SetKorpusMaterial.arguments.length>=2)
			UpdatePrice();
	}

	function SetCurrentVstavkaMaterial(matName,event)
	{
		if (SetCurrentVstavkaMaterial.arguments.length>=2)
		{
			if (((event.target.tagName=='DIV')||(event.target.tagName=='IMG'))&&(event.target.id.substring(0,4)=='mat_'))
			{
				matName=event.target.id.substring(4);
				LoadMaterialByName(matName);
			}
			else
				return;
		}

		CurrentVstavkaMaterial=matName;

		if (SetCurrentVstavkaMaterial.arguments.length>=2)
		{
			var td=event.target?event.target.offsetParent:null;
			if (td.tagName=='TD')
			{
				UnborderTableCells(td.offsetParent,td)
				td.style.borderColor='#ff9e18';
			}
		}
	}

	function SetProfileMaterial(matName,event)
	{
		var matObj=LoadMaterialByName(matName);
		Korpus3dHolder.children[1].material=matObj;
		KupeParams.ProfileMaterial=matName;
		for(var i=0;i<KupeParams.nDoors;i++)
			if (Doors3dHolders[i].children.length>0)
				Doors3dHolders[i].children[0].material=matObj;

		if (SetProfileMaterial.arguments.length>=2)
		{
			var td=event.target?event.target.offsetParent:null;
			if (td.tagName=='TD')
			{
				UnborderTableCells(td.offsetParent,td)
				td.style.borderColor='#ff9e18';
			}
			UpdatePrice();
		}
	}

	function SwitchLamp(event)
	{
		KupeParams.Lamp=1-KupeParams.Lamp;

		if (SwitchLamp.arguments.length>=1)
		{
			var td=event.target?event.target.offsetParent:null;
			if (td.tagName=='TD')
				td.style.borderColor=KupeParams.Lamp?'#ff9e18':'#ffffff';
		}
		for(var k=0;k<Lamp3dHolder.children.length;k++)
		{
			for(var i=0;i<Lamp3dHolder.children[k].children.length;i++)
				Lamp3dHolder.children[k].children[i].visible=KupeParams.Lamp?true:false;
		}

	}

	function DoActivateTab(tabActive,tabSecond)
	{
		for(var i=0;i<DoActivateTab.arguments.length;i++)
		{
			var divTag=document.getElementById('Planner3d_'+DoActivateTab.arguments[i]);
			var headerTag=document.getElementById('Planner3d_'+DoActivateTab.arguments[i]+'Header');
			if (divTag && headerTag)
			{
				headerTag.style.borderBottom=(i==0)?'2px #ff9e18 solid':'0px';
				divTag.style.display=(i==0)?'block':'none';
				headerTag.style.color=(i==0)?'#000000':'#666666';
				headerTag.style.cursor=(i==0)?'default':'pointer';
				headerTag.style.fontWeight=(i==0)?'bold':'normal';
			}
		}
	}

	function GetKorpusPossibleMaterials(KorpusName)
	{
		if (MainJsonData.articuls)
		{
			for(var ii=0;ii<MainJsonData.articuls.korpuses.length;ii++)
			{
				if (MainJsonData.articuls.korpuses[ii].name==KorpusName)
					return GetColorsArrayFromVariants(MainJsonData.articuls.korpuses[ii].variants);
			}
			return ["oak_milk"];
		}
		else
			return ["oak_milk","oak_ferrara","apple_locarno"];
	}

	function GetVstavkaPossibleMaterials(DoorType,DoorVariant,iSection)
	{
		var out=[];
		if (MainJsonData.articuls)
		{
			var Door=GetDoorByType(DoorType);
			var VstavkaHeight=0;
			if (DoorVariant)
			{
				var DoorVariants=GetDoorVariants(Door);
				for(var k=0;k<DoorVariants.length;k++)
				{
					if (DoorVariants[k].type==DoorVariant)
					{
						VstavkaHeight=DoorVariants[k].sections[iSection].height;
						break;
					}
				}
			}

			var clrs=[];
			for(var iv=0;iv<MainJsonData.articuls.vstavki.length;iv++)
			{
				if ((MainJsonData.articuls.vstavki[iv].width==Door.width) && ((VstavkaHeight==0) || (MainJsonData.articuls.vstavki[iv].height==VstavkaHeight)))
				{
					for(var ik=0;ik<MainJsonData.articuls.vstavki[iv].variants.length;ik++)
					{
						if (!ArrayHasItem(clrs,MainJsonData.articuls.vstavki[iv].variants[ik].color))
							clrs.push(MainJsonData.articuls.vstavki[iv].variants[ik].color);
					}
				}
			}
			for(var k=0;k<MainJsonData.materials.length;k++)
			{
				if (ArrayHasItem(clrs,MainJsonData.materials[k].name))
					out.push(MainJsonData.materials[k].name);
			}
		}
		else
		{
			for(var k=0;k<MainJsonData.materials.length;k++)
			{
				if (MainJsonData.materials[k].name.indexOf('Profile')!=0)
					out.push(MainJsonData.materials[k].name);
			}
		}
		return out;
	}

	function GetColorsArrayFromVariants(variants)
	{
		var out=[];
		for(var k=0;k<variants.length;k++)
			out.push(variants[k].color);
		return out;
	}

	function GetProfilePossibleMaterials(KorpusName)
	{
		if (MainJsonData.articuls)
		{
			var Korpus=GetKorpusByName(KorpusName);
			if (Korpus)
			{
				for(var ii=0;ii<MainJsonData.articuls.profiles.length;ii++)
				{
					if (Korpus.width==MainJsonData.articuls.profiles[ii].width)
						return GetColorsArrayFromVariants(MainJsonData.articuls.profiles[ii].variants);
				}
			}
			return ["ProfileSilver"];//"ProfileBronza"
		}
		else
			return ["ProfileSilver","ProfileBronza","ProfileZoloto","ProfileShampan"];
	}

	function GetMaterial(MatName)
	{
		for(var k=0;k<MainJsonData.materials.length;k++)
		{
			if (MainJsonData.materials[k].name==MatName)
				return MainJsonData.materials[k];
		}
	}

	function GetDoorVariant(varType,variants)
	{
		if (varType!=undefined)
		{
			for(var k=0;k<variants.length;k++)
				if (variants[k].type==varType)
					return variants[k];
		}
	}

	function GetKorpusByName(KorpusName)
	{
		for(var k=0;k<MainJsonData.korpuses.length;k++)
			if (MainJsonData.korpuses[k].name==KorpusName)
				return MainJsonData.korpuses[k];
		
	}

	function GetDoorByType(DoorType)
	{
		for(var k=0;k<MainJsonData.doors.length;k++)
		{
			if (MainJsonData.doors[k].type==DoorType)
				return MainJsonData.doors[k];
		}
		
	}

	function ClearChildren(node)
	{
		for(var k=node.children.length-1;k>=0;k--)
			node.remove(node.children[k]);
	}

	function GetDoorVariants(Door)
	{
		if (MainJsonData.articuls)
		{
			var out=[];
			for(var ii=0;ii<MainJsonData.articuls.doors.length;ii++)
			{
				if (MainJsonData.articuls.doors[ii].name==Door.type)
				{
					var types=MainJsonData.articuls.doors[ii].types;
					for(var k=0;k<Door.variants.length;k++)
					{
						for(var jj=0;jj<types.length;jj++)
						{
							if (Door.variants[k].type==types[jj].type)
							{
								out.push(Door.variants[k]);
								break;
							}
					
						}
					}
				}
			}
			return out;
		}
		return Door.variants;
	}

	function UpdateKupeDoor(n)
	{
		var Korpus=GetKorpusByName(KupeParams.KorpusName);
		if (Korpus!=undefined)
		{
			var Door=GetDoorByType(Korpus.doors.type);
			var DoorVariants=GetDoorVariants(Door);

			ClearChildren(Doors3dHolders[n]);

			var DoorVaraint=GetDoorVariant(KupeParams.doors[n].variantType,DoorVariants);
			if (DoorVaraint!=undefined)
			{
				KupeParams.doors[n].sections=DoorVaraint.sections;
				for(var i=0;i<KupeParams.doors[n].sections.length;i++)
				{
					var colors=GetVstavkaPossibleMaterials(KupeParams.DoorsType,KupeParams.doors[n].variantType,i);
					if (!ArrayHasItem(colors,KupeParams.doors[n].materials[i]))
						KupeParams.doors[n].materials[i]=colors[0];

				}

				Doors3dHolders[n].position.z=((n%2)==0)?Door.shiftSecondRail:0;
				if (n==0)
					Doors3dHolders[n].position.x=-0.5*Korpus.width+Korpus.DspThickness+Door.width*0.5;
				else if (n==(Korpus.doors.count-1))
					Doors3dHolders[n].position.x=0.5*Korpus.width-Korpus.DspThickness-Door.width*0.5;
				else
					Doors3dHolders[n].position.x=0;

				for(var k=0;k<DoorVaraint.shapes.length;k++)
				{
					loader.createModel( DoorVaraint.shapes[k], function ( geometry,materials ) {
						OnShapeLoaded('Door'+n,k,geometry,materials);

					}, '' );
				}
				UpdateKupeDoorMaterial(n);
			}
		}
	}

function UpdateKupeDoorMaterial(n,iSection)
{
	var door_chld=Doors3dHolders[n].children;
	if (UpdateKupeDoorMaterial.arguments.length>=2)
	{
		if (iSection<KupeParams.doors[n].sections.length)
		{
			if ((door_chld.length>iSection+1) && (KupeParams.doors[n].materials[iSection]!=undefined) && KupeParams.doors[n].materials[iSection].length)
				door_chld[1+iSection].material=LoadMaterialByName(KupeParams.doors[n].materials[iSection]);
		}
	}
	else
	{
		if (door_chld.length && (KupeParams.ProfileMaterial!=undefined))
			door_chld[0].material=LoadMaterialByName(KupeParams.ProfileMaterial);
		for(var k=0;k<KupeParams.doors[n].sections.length;k++)
		{
			if ((door_chld.length>k+1) && (KupeParams.doors[n].materials[k]!=undefined) && KupeParams.doors[n].materials[k].length)
				door_chld[1+k].material=LoadMaterialByName(KupeParams.doors[n].materials[k]);
		}
	}
}

function UpdateKorpusesDiv()
{
	var syn='<table cellpadding=0 cellspacing=0>';
	if (iActiveTab==1)
	{
		var k;
		var KorpusSizesInfo=[];
		for(k=0;k<MainJsonData.korpuses.length;k++)
		{
			var sz=Math.round(MainJsonData.korpuses[k].width*1000);
			var l;
			for(l=0;(l<KorpusSizesInfo.length) && (sz>KorpusSizesInfo[l][0]);l++){}
			if (l==KorpusSizesInfo.length)
				KorpusSizesInfo.push(new Array(sz,MainJsonData.korpuses[k]));
			else if (sz==KorpusSizesInfo[l][0])
				KorpusSizesInfo[l].push(MainJsonData.korpuses[k]);
			else 
			{
				KorpusSizesInfo.length++;
				for(var i=KorpusSizesInfo.length-1;i>l;i--)
					KorpusSizesInfo[i]=KorpusSizesInfo[i-1];
				KorpusSizesInfo[l]=new Array(sz,MainJsonData.korpuses[k]);
			}
		}

		var CurrentKorpus=GetKorpusByName(KupeParams.KorpusName);
		var CurrentKorpusWidth=Math.round(CurrentKorpus.width*1000);

		for(var iRow=0;iRow<2;iRow++)
		{
			syn+='<tr>';
			for(k=0;k<KorpusSizesInfo.length;k++)
			{
				var Korp=KorpusSizesInfo[k][1];
				if (KorpusSizesInfo[k][0]==CurrentKorpusWidth)
					Korp=CurrentKorpus;

				syn+='<td align=center style="cursor:pointer;';
				var clrBorder=(KorpusSizesInfo[k][0]==CurrentKorpusWidth)?'#ff9e18':'#ffffff';
					syn+='border-'+((iRow==0)?'top':'bottom')+':2px solid '+clrBorder+';border-left:2px solid '+clrBorder+';border-right:2px solid '+clrBorder+';';
				if (iRow==1)
					syn+='font-family:Arial;font-size:9px;';
				syn+='" onmouseover="Planner3dKupeConstructor.showPopupMenu(\'door_'+KorpusSizesInfo[k][0]+'\',event.target)" onmouseout="Planner3dKupeConstructor.OnPopupOut()"';
				if (iRow==0)
					syn+=' valign=bottom width=80 height=47 onclick="Planner3dKupeConstructor.SetKorpus(\''+Korp.name+'\')"><img ondragstart="Planner3dKupeConstructor.stopStart(event)" src="'+HostImgs+'korpus_'+KorpusSizesInfo[k][0]+'.png" >';//title=\"'+Korp.name+'\"
				else 
					syn+='>'+KorpusSizesInfo[k][0]+' мм';
				syn+='</td>';
			}
			syn+='</tr>';
		}
	}
	if (iActiveTab==2)
	{
			syn+='<tr>';

			for(var n=0;n<KupeParams.nDoors;n++)
			{
				var KupeDoor=KupeParams.doors[n];

				syn+='<td align=center style="cursor:pointer;" onmouseover="Planner3dKupeConstructor.showPopupMenu(\'dsb_'+n+'\',event.target)" onmouseout="Planner3dKupeConstructor.OnPopupOut()" valign=bottom width=40 height=43><img ondragstart="Planner3dKupeConstructor.stopStart(event)" src="'+HostImgs+'icon_door_variant_'+KupeDoor.variantType+'.gif" width=36 height=36>';
				syn+='</td>';
			}
			syn+='</tr>';

	}
	syn+='</table>';
	document.getElementById('Planner3d_KorpusVariantsDiv').innerHTML=syn;
	if (cPopupMenu && (iActiveTab==1) && (cPopupMenu.substring(0,5)=='door_'))
		CreateDoorPopup(PopupMenuDiv,parseInt(cPopupMenu.substring(5)));
	if (cPopupMenu && (iActiveTab==2) && (cPopupMenu.substring(0,4)=='dsb_'))
		CreateDsbPopup(PopupMenuDiv,parseInt(cPopupMenu.substring(4)));

}

function SetKorpus(KorpusName)
{
	KupeParams.KorpusName=KorpusName;
	CreateFullKupe();
	UpdateKorpusesDiv();
	UpdatePrice();
}

function CreateFullKupe()
{
	ClearChildren(Korpus3dHolder);
	ClearChildren(Lamp3dHolder);

	var Korpus=GetKorpusByName(KupeParams.KorpusName);

	var nLamps=(Korpus.width>1.5)?3:2;
	for(var k=0;k<nLamps;k++)
	{
		var LampTrn=new THREE.Object3D();
		LampTrn.position.x=-0.5*Korpus.width+(k+0.5)*Korpus.width/nLamps;
		Lamp3dHolder.add( LampTrn );
	}
	

	KupeParams.nDoors=Korpus.doors.count;
	if ((iActiveDoor==undefined)||(iActiveDoor>=KupeParams.nDoors))
		iActiveDoor=0;

	KupeParams.DoorsType=Korpus.doors.type;

	for(var k=0;k<Korpus.shapes.length;k++)
	{
		loader.createModel( Korpus.shapes[k], function ( geometry,materials ) {
			OnShapeLoaded('Korpus',k,geometry,materials);

		}, '' );
	}

	for(var k=0;k<MainJsonData.lamp.length;k++)
	{
		loader.createModel( MainJsonData.lamp[k], function ( geometry,materials ) {
			OnShapeLoaded('Lamp',k,geometry,materials);
		}, '' );
	}

	var Door=GetDoorByType(KupeParams.DoorsType);
	if (Door)
	{
		var DoorVariants=GetDoorVariants(Door);
		for(var n=0;n<Doors3dHoldersMax;n++)
		{
			if (n<Korpus.doors.count)
			{
				if (GetDoorVariant(KupeParams.doors[n].variantType,DoorVariants)==undefined)
					KupeParams.doors[n].variantType=DoorVariants[0].type;
				UpdateKupeDoor(n);
			}
			else
				ClearChildren(Doors3dHolders[n]);
		}
	}
	SetKorpusMaterial(KupeParams.KorpusMaterial);
	SetProfileMaterial(KupeParams.ProfileMaterial);
}


var bPanActive=0;
var fPanDistance;
var last_pan_X;
var last_pan_Y;

function onDocumentMouseDown( event ) 
{
	var clientX=event.pageX-getGlobalOffsetX(MainDiv);
	var clientY=event.pageY-getGlobalOffsetY(MainDiv);
	 if (event.button==2)
	 {
		 event.preventDefault();
		
		if (!MSIE || (MSIE>=11))
		{
			bMouseNavigationActive=true;
			document.addEventListener( 'mouseup', onDocumentMouseUp, false );
			document.addEventListener( 'mouseout', onDocumentMouseOut, false );

			mouseXOnMouseDown = clientX - windowHalfX;
			targetRotationOnMouseDown = NormAngle(targetRotation);
			bMouseMoveBetweenClick=0;
		}
	}
	else if (event.button==0)
	{
		if (iActiveTab==2)
		{
			if (((clientX>=0)&&(clientX<Window3dWidth)) && ((clientY>=0)&&(clientY<Window3dHeight)))
			{
				mouse2D.x = ( clientX / Window3dWidth ) * 2 - 1;
				mouse2D.y = - ( clientY / Window3dHeight ) * 2 + 1;

				raycaster = projector.pickingRay( mouse2D.clone(), camera );

				var intersects = raycaster.intersectObjects( scene.children,true );

				if (1 && intersects.length && (intersects[0].object.parent==Korpus3dHolder))
				{
				}
				else if (intersects.length)
				{
					for(var n=0;n<KupeParams.nDoors;n++)
					{
						if (intersects[0].object.parent==Doors3dHolders[n])
						{
							var Door=GetDoorByType(KupeParams.DoorsType)
							if (Door!=undefined)
							{
								for(var k=0;k<KupeParams.doors[n].sections.length;k++)
								{
									if (intersects[0].object==Doors3dHolders[n].children[1+k])
									{
										var colors=GetVstavkaPossibleMaterials(KupeParams.DoorsType,KupeParams.doors[n].variantType,k);
										if (ArrayHasItem(colors,CurrentVstavkaMaterial))
										{
											KupeParams.doors[n].materials[k]=CurrentVstavkaMaterial;
											UpdateKupeDoorMaterial(iActiveDoor,k);
											UpdatePrice();
										}
										break;
									}
								}
							}
						}
					}
				}
			}
		}
	}
	else if (event.button==1)
	{
		if (((clientX>=0)&&(clientX<Window3dWidth)) && ((clientY>=0)&&(clientY<Window3dHeight)))
		{
			event.preventDefault();
			mouse2D.x = ( clientX / Window3dWidth ) * 2 - 1;
			mouse2D.y = - ( clientY / Window3dHeight ) * 2 + 1;

			raycaster = projector.pickingRay( mouse2D.clone(), camera );

			var intersects = raycaster.intersectObjects( scene.children,true );
			if (intersects.length)
			{
				var dv=intersects[ 0 ].point.clone().sub(camera.position);
				var quaternion = new THREE.Quaternion();
				quaternion.setFromEuler(camera.rotation);

				var ortZ=new THREE.Vector3(0,0,-1);
				ortZ.applyQuaternion( quaternion );

				fPanDistance=dv.dot(ortZ);
			}
			else
				fPanDistance=cameraDistance;


			last_pan_X=clientX;
			last_pan_Y=clientY;
			bPanActive=true;

			document.addEventListener( 'mouseup', onDocumentMouseUp, false );
			document.addEventListener( 'mouseout', onDocumentMouseOut, false );
		}
	}
}

function onDocumentMouseMove( event ) {

	var clientX=event.pageX-getGlobalOffsetX(MainDiv);
	var clientY=event.pageY-getGlobalOffsetY(MainDiv);

	if (bMouseNavigationActive)
	{
		bMouseMoveBetweenClick=1;
		mouseX = clientX - windowHalfX;

		targetRotation = targetRotationOnMouseDown + ( mouseX - mouseXOnMouseDown ) * 0.02;
	}
	if (bPanActive && ((last_pan_X!=clientX)||(last_pan_Y!=clientY)))
	{
		var rvLast = new THREE.Vector3( ( 1.0*last_pan_X / Window3dWidth ) * 2 - 1, - ( 1.0*last_pan_Y / Window3dHeight ) * 2 + 1, 0.5 );
		projector.unprojectVector( rvLast, camera );
		rvLast.sub(camera.position);
		var rvCur = new THREE.Vector3( ( 1.0*clientX / Window3dWidth ) * 2 - 1, - ( 1.0*clientY / Window3dHeight ) * 2 + 1, 0.5 );
		projector.unprojectVector( rvCur, camera );
		rvCur.sub(camera.position);

		var quaternion = new THREE.Quaternion();
		quaternion.setFromEuler(camera.rotation);

		var CamOrtZ=new THREE.Vector3(0,0,-1);
		CamOrtZ.applyQuaternion( quaternion );

		var frvLast=rvLast.dot(CamOrtZ);
		var frvCur=rvCur.dot(CamOrtZ);
		if (1 && (frvLast>0)&&(frvCur>0))
		{
			camera.position.y+=fPanDistance*(rvLast.y/frvLast-rvCur.y/frvCur);

			var sina=Math.sin(camera.rotation.y);
			var cosa=Math.cos(camera.rotation.y);

			cameraXshift+=fPanDistance*(rvLast.x/frvLast-rvCur.x/frvCur)*cosa-fPanDistance*(rvLast.z/frvLast-rvCur.z/frvCur)*sina;

			last_pan_X=clientX;
			last_pan_Y=clientY;
		}



	}
	On3dMouseMove(clientX,clientY);
	event.stopPropagation();
}

function On3dMouseMove(clientX,clientY)
{
	var iActiveDoorPrev=iActiveDoor;
	if (((clientX>=0)&&(clientX<Window3dWidth)) && ((clientY>=0)&&(clientY<Window3dHeight)))
	{
		if (iActiveTab==2)
		{
			mouse2D.x = ( clientX / Window3dWidth ) * 2 - 1;
			mouse2D.y = - ( clientY / Window3dHeight ) * 2 + 1;

			raycaster = projector.pickingRay( mouse2D.clone(), camera );

			var intersects = raycaster.intersectObjects( scene.children,true );

			if (1 && intersects.length && (intersects[0].object.parent==Korpus3dHolder))
			{
			}
			else if (intersects.length)
			{
				for(var n=0;n<KupeParams.nDoors;n++)
				{
					if (intersects[0].object.parent==Doors3dHolders[n])
					{
						iActiveDoor=n;
						var Door=GetDoorByType(KupeParams.DoorsType)
						if (Door!=undefined)
						{
							for(var k=0;k<KupeParams.doors[n].sections.length;k++)
							{
								if (intersects[0].object==Doors3dHolders[n].children[1+k])
								{
									iActiveDoorSection=k;
									break;
								}
							}
							if (k==KupeParams.doors[n].sections.length)
								iActiveDoorSection=-1;
						}
						break;
					}
				}
			}
		}
	}
	else
		iActiveDoor=-1;
	
	if (iActiveDoor>=0)
		showPopupMenu('ds_'+iActiveDoor);
	else if ((iActiveDoorPrev!=iActiveDoor) && (cPopupMenu!=undefined) && (cPopupMenu.substring(0,3)=='ds_'))
		OnPopupOut();
}

function onDocumentContextMenu()
{
	//if (bMouseMoveBetweenClick)
		event.preventDefault();
}

function onDocumentMouseUp( event ) 
{
	if ((event.button==2)||(event.button==1))
	{
		event.preventDefault();

		bMouseNavigationActive=false;
		bPanActive=false;
		document.removeEventListener( 'mouseup', onDocumentMouseUp, false );
		document.removeEventListener( 'mouseout', onDocumentMouseOut, false );
	}
}

function onDocumentMouseOut( event ) {

	bMouseNavigationActive=false;
	bPanActive=false;
	document.removeEventListener( 'mouseup', onDocumentMouseUp, false );
	document.removeEventListener( 'mouseout', onDocumentMouseOut, false );

}

function onDocumentTouchStart( event ) {

	if ( event.touches.length == 1 ) 
	{
		event.preventDefault();

		mouseXOnMouseDown = event.touches[ 0 ].pageX - windowHalfX;
		targetRotationOnMouseDown = NormAngle(targetRotation);

	}

}

function onDocumentTouchMove( event ) {

	if ( event.touches.length == 1 ) 
	{
		event.preventDefault();

		mouseX = event.touches[ 0 ].pageX - windowHalfX;
		targetRotation = targetRotationOnMouseDown + ( mouseX - mouseXOnMouseDown ) * 0.05;

	}

}

function onMouseWheel( event ) {
	if (event.wheelDelta>0)
		cameraDistance*=(1-Math.min(0.2,0.0005*event.wheelDelta));
	else
		cameraDistance/=(1-Math.min(0.2,-0.0005*event.wheelDelta));
	event.preventDefault();
	event.stopPropagation();
}

function hidePopup()
{
	PopupMenuDiv.style.display='none';
	if ((cPopupMenu!=undefined) && ((cPopupMenu.substring(0,5)=='door_')||(cPopupMenu.substring(0,4)=='mat_'))||(cPopupMenu.substring(0,4)=='dsb_'))
	{
		document.getElementById('Planner3d_ProfileVariantsDiv').style.visibility='visible';
		document.getElementById('Planner3d_LampVariantsDiv').style.visibility='visible';
		document.getElementById('Planner3d_TabInfo1').style.visibility='visible';
		document.getElementById('Planner3d_TabInfo2').style.visibility='visible';
	}
	if (hideTimeout!=null)
	{
		clearTimeout(hideTimeout);
		hideTimeout=null;
	}
	cPopupMenu=null;
}

function getGlobalOffsetX(_obj,_parent)
{
	var ps=0;
	var obj;
	for(obj=_obj;(obj.tagName!="BODY") && (obj!=_parent);obj=obj.offsetParent)
		ps+=obj.offsetLeft;
	return ps;
}

function getGlobalOffsetY(_obj,_parent)
{
	var ps=0;
	var obj;
	for(obj=_obj;(obj.tagName!="BODY") && (obj!=_parent);obj=obj.offsetParent)
		ps+=obj.offsetTop;
	return ps;
}

function OnPopupOut()
{
	if (hideTimeout==null)
		hideTimeout = setTimeout(hidePopup,100);
}

function CreateDoorPopup(popupMenu,sz)
{
	var CurrentKorpus=GetKorpusByName(KupeParams.KorpusName);

	var syn='<table cellpadding=0 cellspacing=0>';
	syn+='';
	for(var k=0;k<MainJsonData.korpuses.length;k++)
	{
		if (sz==Math.round(MainJsonData.korpuses[k].width*1000))
		{
			syn+='<tr>';
			for(var iCell=0;iCell<2;iCell++)
			{
				syn+='<td style="cursor:pointer;padding-left:4px;padding-top:4px;padding-right:4px;';
				if (iCell==1)
					syn+='font-family:Arial;font-size:9px;color:#666666;';

				var clrBorder=(MainJsonData.korpuses[k]==CurrentKorpus)?'#ff9e18':'#ffffff';
					syn+='border-'+((iCell==0)?'left':'right')+':2px solid '+clrBorder+';border-top:2px solid '+clrBorder+';border-bottom:2px solid '+clrBorder+';';

				syn+='" ';
				if (iCell==0)
					syn+=' align=center';
				syn+=' onclick="Planner3dKupeConstructor.SetKorpus(\''+MainJsonData.korpuses[k].name+'\')">';
				if (iCell==0)
					syn+='<img src="'+MainJsonData.korpuses[k].icon+'" height=37>';
				else
					syn+='глубина '+Math.round(MainJsonData.korpuses[k].depth*1000)+' мм<br>'+MainJsonData.korpuses[k].doors.count+' двери';
				syn+='</td>';
			}
			syn+='</tr>';
		}
	}
	syn+='';
	syn+='</table>';

	popupMenu.innerHTML=syn;
}

function CreateDsbPopup(popupMenu,sid)
{
	var sid=parseInt(cPopupMenu.substring(4));

	var syn='<table cellpadding=0 cellspacing=0 style="background-color:#FFFFFF">';

	var Door=GetDoorByType(KupeParams.DoorsType);
	var DoorVariants=GetDoorVariants(Door);
	if (Door)
	{
		for(var k=0;k<DoorVariants.length;k++)
		{
			syn+='<tr>';
			for(var iCell=0;iCell<2;iCell++)
			{
				syn+='<td style="cursor:pointer;padding-left:4px;padding-top:4px;padding-right:4px;';
				if (iCell==1)
					syn+='font-family:Arial;font-size:9px;color:#666666;';

				var clrBorder=(KupeParams.doors[sid].variantType==DoorVariants[k].type)?'#ff9e18':'#ffffff';
					syn+='border-'+((iCell==0)?'left':'right')+':2px solid '+clrBorder+';border-top:2px solid '+clrBorder+';border-bottom:2px solid '+clrBorder+';';

				syn+='" ';
				if (iCell==0)
					syn+=' align=center';
				syn+=' onclick=\'Planner3dKupeConstructor.OnClickSmallButt(event,"set_variant","'+DoorVariants[k].type+'","'+sid+'")\'>';
				if (iCell==0)
					syn+='<img src="'+HostImgs+'icon_door_variant_'+DoorVariants[k].type+'.gif" width=36 height=36 border=0>';
				else
				{
					if (DoorVariants[k].type==1)
						syn+=DoorVariants[k].type+' секция';
					else
						syn+=DoorVariants[k].type+' секции';
				}
				syn+='</td>';
			}
			syn+='</tr>';

		}
	}
	syn+='</table>';
	popupMenu.innerHTML=syn;
}


function showPopupMenu(pmType,srcElement)
{
	if (hideTimeout!=null)
	{
		clearTimeout(hideTimeout);
		hideTimeout=null;
	}
	if ((showPopupMenu.arguments.length==0) || (pmType instanceof Event))
		return;
	if (cPopupMenu!=pmType)
	{
		cPopupMenu=pmType;

		if ((cPopupMenu.substring(0,5)=='door_')||(cPopupMenu.substring(0,4)=='mat_')||(cPopupMenu.substring(0,4)=='dsb_'))
		{
			if (cPopupMenu.substring(0,4)=='mat_')
			{
				document.getElementById('Planner3d_ProfileVariantsDiv').style.visibility='hidden';
				document.getElementById('Planner3d_LampVariantsDiv').style.visibility='hidden';
			}
			document.getElementById('Planner3d_TabInfo1').style.visibility=(cPopupMenu.substring(0,4)!='mat_')?'hidden':'visible';
			document.getElementById('Planner3d_TabInfo2').style.visibility=(cPopupMenu.substring(0,4)!='mat_')?'hidden':'visible';
		}
		else if (cPopupMenu.substring(0,3)=='ds_')
		{
			document.getElementById('Planner3d_ProfileVariantsDiv').style.visibility='visible';
			document.getElementById('Planner3d_LampVariantsDiv').style.visibility='visible';
			document.getElementById('Planner3d_TabInfo1').style.visibility='visible';
			document.getElementById('Planner3d_TabInfo2').style.visibility='visible';
		}

		var popupMenu=PopupMenuDiv;

		popupMenu.style.display='block';

		if (cPopupMenu.substring(0,5)=='door_')
		{
			CreateDoorPopup(popupMenu,parseInt(cPopupMenu.substring(5)));

			var cPopupLeft=getGlobalOffsetX(srcElement,MainDiv);

			var kvd=document.getElementById('Planner3d_KorpusVariantsDiv');
			if (cPopupLeft+popupMenu.offsetWidth>getGlobalOffsetX(kvd,MainDiv)+kvd.offsetWidth-1)
				cPopupLeft=getGlobalOffsetX(kvd,MainDiv)+kvd.offsetWidth-popupMenu.offsetWidth-1;


			popupMenu.style.left=cPopupLeft+'px';
			popupMenu.style.top=(getGlobalOffsetY(kvd,MainDiv)+kvd.offsetHeight)+'px';
			//popupMenu.style.top=(Window3dHeight)+'px';
		}
		else if (cPopupMenu.substring(0,4)=='mat_')
		{
			var mat=GetMaterial(cPopupMenu.substring(4));
			if (mat!=undefined)
			{
				var syn='<div style="width:149px;height:89px;background-image:url('+mat.icon+');"></div>';
				syn+='<div style="width:149px;text-align:center;font-family:Arial;font-size:9px;color:#666666;">'+mat.description+'</div>';

				popupMenu.innerHTML=syn;
			}

			popupMenu.style.left=(getGlobalOffsetX(document.getElementById('Planner3d_LampVariantsDiv'),MainDiv)+44)+'px';
			popupMenu.style.top=(getGlobalOffsetY(document.getElementById('Planner3d_LampVariantsDiv'),MainDiv)+25)+'px';
		}
		else if (cPopupMenu.substring(0,3)=='ds_')
		{
			var sid=parseInt(cPopupMenu.substring(3));

			var syn='<table cellpadding=0 cellspacing=0 style="background-color:#FFFFFF"><tr>'
		
			var Door=GetDoorByType(KupeParams.DoorsType);
			var DoorVariants=GetDoorVariants(Door);
			if (Door)
			{
				for(var k=0;k<DoorVariants.length;k++)
				{
					syn+='<td height=30 valign=middle';
					if (KupeParams.doors[iActiveDoor].variantType==DoorVariants[k].type)
						syn+=' style="border:2px solid #ff9e18;"';
					syn+='><img src="'+HostImgs+'icon_door_variant_'+DoorVariants[k].type+'_sm.gif" style="cursor:pointer" width=20 height=24 border=0 onclick=\'Planner3dKupeConstructor.OnClickSmallButt(event,"set_variant","'+DoorVariants[k].type+'","'+sid+'")\'></td>';
				}
			}
			syn+='</tr></table>';
			popupMenu.innerHTML=syn;
		}
		else if (cPopupMenu.substring(0,4)=='dsb_')
		{
			CreateDsbPopup(popupMenu,parseInt(cPopupMenu.substring(4)))

			var cPopupLeft=getGlobalOffsetX(srcElement,MainDiv);

			var kvd=document.getElementById('Planner3d_KorpusVariantsDiv');
			if (cPopupLeft+popupMenu.offsetWidth>getGlobalOffsetX(kvd,MainDiv)+kvd.offsetWidth)
				cPopupLeft=getGlobalOffsetX(kvd,MainDiv)+kvd.offsetWidth-popupMenu.offsetWidth;


			popupMenu.style.left=cPopupLeft+'px';
			popupMenu.style.top=(getGlobalOffsetY(kvd,MainDiv)+kvd.offsetHeight)+'px';
		}
	}
}

var playTimeInterval=null;
function ClearPlayTimeInterval()
{
	if (playTimeInterval)
	{
		clearInterval(playTimeInterval);
		playTimeInterval=null;
	}
}

function OnPressSmallButtRotateL(event,idStr)
{
	ClearPlayTimeInterval();

	if ((idStr=='')||(idStr=='RotateR'))
		targetRotation= -camera.rotation.y;

	eval('ProcessPressAction(\''+idStr+'\')');
	playTimeInterval = setInterval('ProcessPressAction(\''+idStr+'\')',30);
}

function OnPressSmallButtRotateL(event,idStr)
{
	ClearPlayTimeInterval();
	targetRotation= -camera.rotation.y;
	document.addEventListener( 'mouseup', OnReleaseSmallButt, false );

	ProcessPressActionRotateL();
	playTimeInterval = setInterval(ProcessPressActionRotateL,30);
}
function OnPressSmallButtRotateR(event,idStr)
{
	ClearPlayTimeInterval();
	targetRotation= -camera.rotation.y;
	document.addEventListener( 'mouseup', OnReleaseSmallButt, false );

	ProcessPressActionRotateR();
	playTimeInterval = setInterval(ProcessPressActionRotateR,30);
}
function OnPressSmallButtZoomIn(event,idStr)
{
	ClearPlayTimeInterval();
	document.addEventListener( 'mouseup', OnReleaseSmallButt, false );
	ProcessPressActionZoomIn();
	playTimeInterval = setInterval(ProcessPressActionZoomIn,30);
}
function OnPressSmallButtZoomOut(event,idStr)
{
	ClearPlayTimeInterval();
	document.addEventListener( 'mouseup', OnReleaseSmallButt, false );
	ProcessPressActionZoomOut();
	playTimeInterval = setInterval(ProcessPressActionZoomOut,30);
}

function ProcessPressActionRotateR() { targetRotation+= (10) * 0.02; }
function ProcessPressActionRotateL() { targetRotation-= (10) * 0.02; }

function ProcessPressActionZoomIn() { cameraDistance*=(1-Math.min(0.2,0.0005*5)); }
function ProcessPressActionZoomOut() { cameraDistance/=(1-Math.min(0.2,0.0005*5)); }

function OnReleaseSmallButt()
{
	document.removeEventListener( 'mouseup', OnReleaseSmallButt, false );
	ClearPlayTimeInterval();
}

function OnClickSmallButtColorPicker(event)
{
	OnClickSmallButt(event,'colorpicker',0);
}

function OnClickSmallButt(event,type,param,param2)
{
	if (type=='set_variant')
	{
		KupeParams.doors[param2].variantType=param;
		UpdateKupeDoor(param2);
		var cPopupMenuPrev=cPopupMenu;
		cPopupMenu=undefined;
		if ((cPopupMenuPrev!=undefined)&&(cPopupMenuPrev.substring(0,4)=='dsb_'))
		{
			cPopupMenu=cPopupMenuPrev;
			CreateDsbPopup(PopupMenuDiv,parseInt(cPopupMenu.substring(4)));
		}
		else
			showPopupMenu(cPopupMenuPrev);
		UpdateKorpusesDiv();
		UpdatePrice();
	}
	else if (type=='colorpicker')
	{
		if ((iActiveDoorSection>=0)&&(iActiveDoorSection<KupeParams.doors[iActiveDoor].sections.length))
		{
			CurrentVstavkaMaterial=KupeParams.doors[iActiveDoor].materials[iActiveDoorSection];

			ActivateTab(iActiveTab);
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

var curDragMat=0;

function onDragMatStart(matName, event)
{
	LoadMaterialByName(matName);

	event.dataTransfer.setData('text', matName);
	curDragMat=matName;
	event.stopPropagation();
}

function onDragMatEnd()
{
	curDragMat='';
}

function onDropHandler(event)
{
	var clientX=event.pageX-getGlobalOffsetX(MainDiv);
	var clientY=event.pageY-getGlobalOffsetY(MainDiv);
	if (((clientX>=0)&&(clientX<Window3dWidth)) && ((clientY>=0)&&(clientY<Window3dHeight)))
	{
		mouse2D.x = ( clientX / Window3dWidth ) * 2 - 1;
		mouse2D.y = - ( clientY / Window3dHeight ) * 2 + 1;

		raycaster = projector.pickingRay( mouse2D.clone(), camera );

		var intersects = raycaster.intersectObjects( scene.children,true );

		if (1 && intersects.length && (intersects[0].object.parent==Korpus3dHolder))
		{
			return;
		}
		else if (intersects.length)
		{
			for(var n=0;n<KupeParams.nDoors;n++)
			{
				if (intersects[0].object.parent==Doors3dHolders[n])
				{
					var Door=GetDoorByType(KupeParams.DoorsType)
					if (Door!=undefined)
					{
						for(var k=0;k<KupeParams.doors[n].sections.length;k++)
						{
							if (intersects[0].object==Doors3dHolders[n].children[1+k])
							{
								var colors=GetVstavkaPossibleMaterials(KupeParams.DoorsType,KupeParams.doors[n].variantType,k);
								if (ArrayHasItem(colors,curDragMat))
								{
									KupeParams.doors[n].materials[k]=curDragMat;
									UpdateKupeDoorMaterial(n,k);
									UpdatePrice();
								}
								break;
							}
						}
					}

					return;
				}
			}

		}
	}
}

function onDragOver(event)
{
	var matName=event.dataTransfer.getData('text');
	if (curDragMat && curDragMat.length)
	{
		if (event.preventDefault)
		{
    		event.preventDefault();
    	}
		event.dataTransfer.dropEffect='copy';
	}
	else
		event.dataTransfer.dropEffect='none';
}

function onDragEnter(event)
{
	if (curDragMat && curDragMat.length)
	{
		if (event.preventDefault)
		{
    		event.preventDefault();
    	}
		event.dataTransfer.dropEffect='copy';
	}
	else
		event.dataTransfer.dropEffect='none';

	return false;
}

function Project3dPointTo2d(x,y,z)
{
	var vector = projector.projectVector( new THREE.Vector3( x,y,z ), camera );
	return {"x":Math.round(vector.x *Window3dWidth*0.5+Window3dWidth/2), "y": Math.round(Window3dHeight/2-vector.y *Window3dHeight*0.5)};
}

function animate() {
	requestAnimationFrame( animate );

	render();

	if (iActiveTab==2)
	{
		if ((Doors3dHolders[0].children.length>0) && (iActiveDoor>=0))
		{
			if ((cPopupMenu!=undefined) && (cPopupMenu.substring(0,3)=='ds_'))
			{
				Doors3dHolders[iActiveDoor].children[0].geometry.computeBoundingBox();
				var bb = Doors3dHolders[iActiveDoor].children[0].geometry.boundingBox;
				var pnt2d=Project3dPointTo2d(0.5*(bb.min.x+bb.max.x)+Doors3dHolders[iActiveDoor].position.x, bb.max.y+Doors3dHolders[iActiveDoor].position.y, bb.max.z+Doors3dHolders[iActiveDoor].position.z);

				var pm=PopupMenuDiv;
				if (pnt2d.x-30<0)
					pnt2d.x=30;
				if (pnt2d.x+30>=Window3dWidth)
					pnt2d.x=Window3dWidth-30;
				if (pnt2d.y<16)
					pnt2d.y=16;
				if (pnt2d.y+28>=Window3dHeight)
					pnt2d.y=Window3dHeight-28;
				pm.style.left=(pnt2d.x-30)+'px';
				pm.style.top=Math.round(pnt2d.y-16)+'px';
			}

			if ((iActiveDoorSection>=0)&&(iActiveDoorSection<KupeParams.doors[iActiveDoor].sections.length))
			{
				Doors3dHolders[iActiveDoor].children[1+iActiveDoorSection].geometry.computeBoundingBox();
				var bb = Doors3dHolders[iActiveDoor].children[1+iActiveDoorSection].geometry.boundingBox;
				var pnt2d=Project3dPointTo2d(0.5*(bb.min.x+bb.max.x)+Doors3dHolders[iActiveDoor].position.x, 0.5*(bb.min.y+bb.max.y)+Doors3dHolders[iActiveDoor].position.y, bb.max.z+Doors3dHolders[iActiveDoor].position.z);
				ButtColorPicker.style.left=(pnt2d.x-13)+'px';
				ButtColorPicker.style.top=Math.round(pnt2d.y-13)+'px';

				ButtColorPicker.style.display='block';
			}
			else
				ButtColorPicker.style.display='none';

		}
		else
			ButtColorPicker.style.display='none';
	}

	if (stats)
		stats.update();

}

function render() {

	var camAng=NormAngle(camera.rotation.y+( -targetRotation - camera.rotation.y ) * 0.05);

	if (Math.abs(camAng)>3.1415927/2*(17.0/18))
		camera.rotation.y = ((camAng>0)?1:-1)*3.1415927/2*(17.0/18);
	else
		camera.rotation.y = camAng;

	var sina=Math.sin(camera.rotation.y);
	var cosa=Math.cos(camera.rotation.y);

	if (cameraDistance<2)
		cameraDistance=2
	else if (cameraDistance>25)
		cameraDistance=25;

	camera.position.x =  sina*cameraDistance+cameraXshift*cosa;
	camera.position.z =  cosa*cameraDistance-cameraXshift*sina;

	renderer.render( scene, camera );
}

	return {
		Initialize:Initialize,
		Uninitialize:Uninitialize,
		SetKorpusMaterial:SetKorpusMaterial,
		SetCurrentVstavkaMaterial:SetCurrentVstavkaMaterial,
		SetProfileMaterial:SetProfileMaterial,
		SetKorpus:SetKorpus,
		OnClickSmallButt:OnClickSmallButt,
		showPopupMenu:showPopupMenu,
		OnPopupOut:OnPopupOut,
		stopStart:stopStart,
		onDragMatStart:onDragMatStart,
		onDragMatEnd:onDragMatEnd
	}
}

	//Progress Bar
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

