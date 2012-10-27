<?php

if (isset($_GET['3141592654'])) die(highlight_file(__FILE__, 1));

function getPageJS($canvas, $width, $height)
{
	$output = '';

	$output .= '<script src="surface.js"></script>';

	$output .= '<script>';

	$output .= "var canvas = '$canvas';";
	$output .= "var width = '$width';";
	$output .= "var height = '$height';";

	$output .= <<< 'EOD'

function randomValue()
{
	if(Math.random() > 0.5)
	{
		return true;
	}
	else
	{
		return false;
	}
}

var length_in_bytes = width*height;

var cells;
var temp_cells;

var birthrule;
var deathrule;

var xoleft, xoright, yoabove, yobelow;
var upleft, up, upright, left, right, downleft, down, downright;

var onColor;
var offColor;
var cycleColors;
var cycleRate;
var cycleBaseDirR;
var cycleBaseDirG;
var cycleBaseDirB;
var cycleCellDirR;
var cycleCellDirG;
var cycleCellDirB;
var baseColorR;
var baseColorG;
var baseColorB;
var colorR;
var colorG;
var colorB;
var cycleFromR;
var cycleFromG;
var cycleFromB;
var cycleToR;
var cycleToG;
var cycleToB;
var cycleChoice;
var running = true;
var generation;
var offsetTemp;
var offsetCopy;
var offsetGeneration;
var x2, y2, count;
var tempColor;

function cycleColorsNext()
{
	switch (cycleChoice)
	{
		case 0:
			cycleBaseDirR?baseColorR++:baseColorR--;
			cycleBaseDirG?baseColorG++:baseColorG--;
			cycleBaseDirB?baseColorB++:baseColorB--;

			if ((baseColorR >= cycleToR) || (baseColorR <= cycleFromR)) cycleBaseDirR ^= 1;
			if ((baseColorG >= cycleToG) || (baseColorG <= cycleFromG)) cycleBaseDirG ^= 1;
			if ((baseColorB >= cycleToB) || (baseColorB <= cycleFromB)) cycleBaseDirB ^= 1;
		break;

		case 1:
			cycleCellDirR?colorR++:colorR--;
			cycleCellDirG?colorG++:colorG--;
			cycleCellDirB?colorB++:colorB--;

			if ((colorR >= cycleToR) || (colorR <= cycleFromR)) cycleCellDirR ^= 1;
			if ((colorG >= cycleToG) || (colorG <= cycleFromG)) cycleCellDirG ^= 1;
			if ((colorB >= cycleToB) || (colorB <= cycleFromB)) cycleCellDirB ^= 1;
		break;

		case 2:
			cycleBaseDirR?baseColorR++:baseColorR--;
			cycleBaseDirG?baseColorG++:baseColorG--;
			cycleBaseDirB?baseColorB++:baseColorB--;

			cycleCellDirR?colorR++:colorR--;
			cycleCellDirG?colorG++:colorG--;
			cycleCellDirB?colorB++:colorB--;

			if ((baseColorR >= cycleToR) || (baseColorR <= cycleFromR)) cycleBaseDirR ^= 1;
			if ((baseColorG >= cycleToG) || (baseColorG <= cycleFromG)) cycleBaseDirG ^= 1;
			if ((baseColorB >= cycleToB) || (baseColorB <= cycleFromB)) cycleBaseDirB ^= 1;

			if ((colorR >= cycleToR) || (colorR <= cycleFromR)) cycleCellDirR ^= 1;
			if ((colorG >= cycleToG) || (colorG <= cycleFromG)) cycleCellDirG ^= 1;
			if ((colorB >= cycleToB) || (colorB <= cycleFromB)) cycleCellDirB ^= 1;

		break;
	}

	onColor = '';
	tempColor = colorR.toString(16);
	onColor += (tempColor.length == 1)?'0'+tempColor:tempColor;
	tempColor = colorG.toString(16);
	onColor += (tempColor.length == 1)?'0'+tempColor:tempColor;
	tempColor = colorB.toString(16);
	onColor += (tempColor.length == 1)?'0'+tempColor:tempColor;
	onColor = '0x'+onColor;

	offColor = '';
	tempColor = baseColorR.toString(16);
	offColor += (tempColor.length == 1)?'0'+tempColor:tempColor;
	tempColor = baseColorG.toString(16);
	offColor += (tempColor.length == 1)?'0'+tempColor:tempColor;
	tempColor = baseColorB.toString(16);
	offColor += (tempColor.length == 1)?'0'+tempColor:tempColor;
	offColor = '0x'+offColor;
}

function setStandardRules()
{
	birthrule = [false, false, false, true, false, false, false, false, false, false, false, false, false, false, false, false];
	deathrule = [true, true, false, false, true, true, true, true, true, true, true, true, true, true, true, true];
}

function setDefaultWeights()
{
	upleft = up = upright = left = right = downleft = down = downright = 1;
}

function setRandomRules()
{
	birthrule = [randomValue(), randomValue(), randomValue(), randomValue(), randomValue(), randomValue(), randomValue(), randomValue(), randomValue(), randomValue(), randomValue(), randomValue(), randomValue(), randomValue(), randomValue(), randomValue()];
	deathrule = [randomValue(), randomValue(), randomValue(), randomValue(), randomValue(), randomValue(), randomValue(), randomValue(), randomValue(), randomValue(), randomValue(), randomValue(), randomValue(), randomValue(), randomValue(), randomValue()];
}

function clearCells()
{
	for(offsetInit = 0; offsetInit < length_in_bytes; offsetInit++)
	{
		cells[offsetInit] = 0;
	}
}

function initialCells()
{
	var originX = Math.floor(width/2);
	var originY = Math.floor(height/2);

	if(randomValue())
	{
		toggle_state(originX, originY);
	}

	if(randomValue())
	{
		toggle_state(originX - 1, originY);
		toggle_state(originX + 1, originY);
		toggle_state(originX, originY - 1);
		toggle_state(originX, originY + 1);
	}

	if(randomValue())
	{
		toggle_state(originX - 1, originY - 1);
		toggle_state(originX + 1, originY + 1);
		toggle_state(originX - 1, originY + 1);
		toggle_state(originX + 1, originY - 1);
	}
}

function automataInit()
{
	width = Number(width);
	height = Number(height);

	cells = new Array(width*height);
	temp_cells = new Array(width*height);

	if(generation == 0)
	{
		setStandardRules();
		setDefaultWeights();
		clearCells();
		initialCells();
	}

	onColor = 0xFFFFFF;
	offColor = 0x000000;
	cycleColors = true;
	cycleRate = 4;
	cycleBaseDirR = true;
	cycleBaseDirG = true;
	cycleBaseDirB = true;
	cycleCellDirR = false;
	cycleCellDirG = false;
	cycleCellDirB = false;
	baseColorR = 0;
	baseColorG = 0;
	baseColorB = 0;
	colorR = 255;
	colorG = 255;
	colorB = 255;
	cycleFromR = 0;
	cycleFromG = 0;
	cycleFromB = 0;
	cycleToR = 255;
	cycleToG = 255;
	cycleToB = 255;
	cycleChoice = 2;
}

function set_cell(x, y)
{
	offsetTemp = (y * width) + x;

	xoleft = (x == 0) ? width - 1 : -1;
	yoabove = (y == 0) ? length_in_bytes - width: -width;
	xoright = (x == (width - 1)) ? -(width - 1) : 1;
	yobelow = (y == (height - 1)) ? -(length_in_bytes - width) : width;

	cells[offsetTemp] |= 0x01;

	cells[offsetTemp + yoabove + xoleft] += (upleft << 1);
	cells[offsetTemp + yoabove] += (up << 1);
	cells[offsetTemp + yoabove + xoright] += (upright << 1);
	cells[offsetTemp + xoleft] += (left << 1);
	cells[offsetTemp + xoright] += (right << 1);
	cells[offsetTemp + yobelow + xoleft] += (downleft << 1);
	cells[offsetTemp + yobelow] += (down << 1);
	cells[offsetTemp + yobelow + xoright] += (downright << 1);

	Surface.plot(x, y, onColor);
}

function clear_cell(x, y)
{
	offsetTemp = (y * width) + x;

	xoleft = (x == 0) ? width - 1 : -1;
	yoabove = (y == 0) ? length_in_bytes - width: -width;
	xoright = (x == (width - 1)) ? -(width - 1) : 1;
	yobelow = (y == (height - 1)) ? -(length_in_bytes - width) : width;

	cells[offsetTemp] &= ~0x01;

	cells[offsetTemp + yoabove + xoleft] -= (upleft << 1);
	cells[offsetTemp + yoabove] -= (up << 1);
	cells[offsetTemp + yoabove + xoright] -= (upright << 1);
	cells[offsetTemp + xoleft] -= (left << 1);
	cells[offsetTemp + xoright] -= (right << 1);
	cells[offsetTemp + yobelow + xoleft] -= (downleft << 1);
	cells[offsetTemp + yobelow] -= (down << 1);
	cells[offsetTemp + yobelow + xoright] -= (downright << 1);

	Surface.plot(x, y, offColor);
}

function cell_state(x, y)
{
	offsetTemp = (y * width) + x;
	return ((cells[offsetTemp] & 0x01) == 1);
}

function set_state(x, y)
{
	if(!cell_state(x, y))
	{
		set_cell(x, y);
	}
}

function clear_state(x, y)
{
	if(cell_state(x, y))
	{
		clear_cell(x, y);
	}
}

function toggle_state(x, y)
{
	if(cell_state(x, y))
	{
		clear_cell(x, y);
	}
	else
	{
		set_cell(x, y);
	}
}

function getInput()
{
	if(Surface.mouseDown)
	{
		set_state(Surface.mouse.x, Surface.mouse.y);
	}
}

function next_generation()
{
	for(offsetCopy = 0; offsetCopy < length_in_bytes; offsetCopy++)
	{
		temp_cells[offsetCopy] = cells[offsetCopy];
	}

	offsetGeneration = 0;

	rowDone:
	for(y2 = 0; y2 < height; y2++)
	{
		x2 = 0;
		do
		{
			while (temp_cells[offsetGeneration] == 0)
			{
				offsetGeneration++;
				if(++x2 >= width)
				{
					continue rowDone;
				}
			}

			count = temp_cells[offsetGeneration] >> 1;

			if((temp_cells[offsetGeneration] & 0x01))
			{
				if(deathrule[count]) clear_cell(x2, y2);
			}
			else
			{
				if(birthrule[count]) set_cell(x2, y2);
			}

			offsetGeneration++;
		}
		while (++x2 < width);
	}

	generation += 1;
}

function automataLoop()
{
//	getInput();

	if(cycleColors && ((generation % cycleRate) == 0))
	{
		cycleColorsNext();
	}

	if(running) next_generation();

	Surface.render();
}

function main(canvas, width, height, mainFunc, loopFunc)
{
	var canvasContext = document.getElementById(canvas);

	Surface.init(canvasContext, width, height);

	generation = 0;

	mainFunc();

	Surface.loop(loopFunc, 60);

	animLoop(function( deltaT, now )
	{
//		automataLoop();
		loopFunc();
	}, canvas );

}

main(canvas, width, height, automataInit, automataLoop);

EOD;
	$output .= '</script>';

	return $output;
}

function getPageHTML($canvas, $width, $height)
{
	$output = '';

	$output .= '<!DOCTYPE html>';
	$output .= '<html>';

		$output .= '<head>';

		$output .= '</head>';

		$output .= '<body>';

		$output .= '<canvas id="'.$canvas.'" width="'.$width.'" height="'.$height.'">';
		$output .= 'herp derp nice browser';
		$output .= '</canvas>';

		$output .= '<br><input id="derp" name="derp" type="button" onClick="clearCells();">Clear</input>';
		$output .= '<input id="herp" name="herp" type="button" onClick="initialCells();">Spawn</input>';
		$output .= '<input id="herp" name="herp" type="button" onClick="running=!running;">Start/Stop</input>';
		$output .= '<input id="nerp" name="nerp" type="button" onClick="setRandomRules();">RandomRules</input>';

		$output .= getPageJS($canvas, $width, $height);

		$output .= '</body>';

	$output .= '</html>';

	return $output;
}

$output = '';

$output .= getPageHTML('canvas', 257, 257);

echo $output;

?>
