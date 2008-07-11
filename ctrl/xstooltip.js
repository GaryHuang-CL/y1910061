function ck(d, c)
{
	location.href = 'http://speed.travian.hk/karte.php?d=' + d + '&c=' + c;
}

function ts(img, pname, pop, axis, vname, ally)
{
	it = document.getElementById('tooltip');
	
	it.innerHTML = vname + '<br/>玩家：' + pname + '<br/>聯盟：' + ally + '<br/>人口：' + pop + '<br/>座標：' + axis;

	posX = 20;
	posY = 0;

	x = img.offsetLeft + posX;
	y = img.offsetTop + posY;
		
	it.style.top = y;
	it.style.left = x;

	
	it.style.visibility = 'visible'; 
}

function th()
{
	it = document.getElementById('tooltip'); 
	it.style.visibility = 'hidden'; 
}


function l(img, x, y)
{
	img.style.position = 'absolute';
	img.style.top  = y * 15;
	img.style.left = x * 15;
}
