// Copyright (c) 2012 Timothy Volp


// Constants
MSIE = '\v' == 'v';

VK_LBUTTON		= 0,
VK_MBUTTON		= 1,
VK_RBUTTON		= 2,

VK_BACKSPACE	= 8,
VK_TAB			= 9,
VK_ENTER		= 13,
VK_SHIFT		= 16,
VK_CTRL			= 17,
VK_ALT			= 18,
VK_PAUSE		= 19,
VK_CAPSLOCK		= 20,
VK_ESCAPE		= 27,
VK_SPACE		= 32,
VK_PAGEUP		= 33,
VK_PAGEDOWN		= 34,
VK_END			= 35,
VK_HOME			= 36,
VK_LEFT			= 37,
VK_UP			= 38,
VK_RIGHT		= 39,
VK_DOWN			= 40,
VK_INSERT		= 45,
VK_DELETE		= 46,
VK_0			= 48,
VK_1			= 49,
VK_2			= 50,
VK_3			= 51,
VK_4			= 52,
VK_5			= 53,
VK_6			= 54,
VK_7			= 55,
VK_8			= 56,
VK_9			= 57,
VK_A			= 65,
VK_B			= 66,
VK_C			= 67,
VK_D			= 68,
VK_E			= 69,
VK_F			= 70,
VK_G			= 71,
VK_H			= 72,
VK_I			= 73,
VK_J			= 74,
VK_K			= 75,
VK_L			= 76,
VK_M			= 77,
VK_N			= 78,
VK_O			= 79,
VK_P			= 80,
VK_Q			= 81,
VK_R			= 82,
VK_S			= 83,
VK_T			= 84,
VK_U			= 85,
VK_V			= 86,
VK_W			= 87,
VK_X			= 88,
VK_Y			= 89,
VK_Z			= 90,
VK_LCOMMAND		= 91,
VK_RCOMMAND		= 92,
VK_SELECT		= 93,
VK_NUMPAD0		= 96,
VK_NUMPAD1		= 97,
VK_NUMPAD2		= 98,
VK_NUMPAD3		= 99,
VK_NUMPAD4		= 100,
VK_NUMPAD5		= 101,
VK_NUMPAD6		= 102,
VK_NUMPAD7		= 103,
VK_NUMPAD8		= 104,
VK_NUMPAD9		= 105,
VK_MULTIPLY		= 106,
VK_ADD			= 107,
VK_SUBTRACT		= 109,
VK_DECIMAL		= 110,
VK_DIVIDE		= 111,
VK_F1			= 112,
VK_F2			= 113,
VK_F3			= 114,
VK_F4			= 115,
VK_F5			= 116,
VK_F6			= 117,
VK_F7			= 118,
VK_F8			= 119,
VK_F9			= 120,
VK_F10			= 121,
VK_F11			= 122,
VK_F12			= 123,
VK_NUMLOCK		= 144,
VK_SCROLLLOCK	= 145,
VK_SEMICOLON	= (MSIE) ? 186 : 59, // Detect IE
VK_EQUALSIGN	= (MSIE) ? 187 : 107,
VK_COMMA		= 188,
VK_DASH			= (MSIE) ? 189 : 109,
VK_PERIOD		= 190,
VK_FORWARDSLASH	= 191,
VK_GRAVEACCENT	= 192,
VK_OPENBRACKET	= 219,
VK_BACKSLASH	= 220,
VK_CLOSEBRACKET	= 221,
VK_SINGLEQUOTE	= 222;

(function( window, Date ) {
var raf = window.mozRequestAnimationFrame ||
          window.webkitRequestAnimationFrame ||
          window.msRequestAnimationFrame ||
          window.oRequestAnimationFrame;

window.animLoop = function( render, element ) {
  var running, lastFrame = +new Date;
  function loop( now ) {
    if ( running !== false ) {
      raf ?
        raf( loop, element ) :
        setTimeout( loop, 16 );
      now = now && now > 1E4 ? now : +new Date;
      var deltaT = now - lastFrame;
      if ( deltaT < 160 ) {
        running = render( deltaT, now );
      }
      lastFrame = now;
    }
  }
  loop();
};
})( window, Date );

var Surface = {

    init: function(canvas, width, height) {
        // Canvas
        canvas.width = width;
        canvas.height = height;
        Surface.context = canvas.getContext('2d');
        Surface.context.fillRect(0, 0, width, height); // Set context to opaque
        Surface.imageData = Surface.context.getImageData(0, 0, width, height);
        Surface.pixelArray = Surface.imageData.data;

        // Globals
        Surface.canvas = canvas;
        Surface.width = width;
        Surface.height = height;
    },

    loop: function(func, fps) {
        var start = new Date().getTime();
        if (!func()) {
            var end = new Date().getTime();
            var sleep = 1000 / fps - (end - start);
            setTimeout(function() {
                Surface.loop(func, fps);
            }, sleep > 0 ? sleep : 0);
        }
    },

    render: function() {
        Surface.context.putImageData(Surface.imageData, 0, 0);
    },

    fill: function(colour) {
        for (var i = Surface.width * Surface.height; i--;) {
            Surface.pixelArray[i << 2] = (colour & 0xFF0000) >> 16;
            Surface.pixelArray[(i << 2) + 1] = (colour & 0xFF00) >> 8;
            Surface.pixelArray[(i << 2) + 2] = colour & 0xFF;
        }
    },

    pixel: function(x, y) {
        var offset = (y * Surface.width + x << 2) + 2;
        return Surface.pixelArray[offset] | Surface.pixelArray[--offset] << 8 | Surface.pixelArray[--offset] << 16;
    },

    plot: function(x, y, colour) {
        var offset = y * Surface.width + x << 2;
        Surface.pixelArray[offset] = (colour & 0xFF0000) >> 16;
        Surface.pixelArray[++offset] = (colour & 0xFF00) >> 8;
        Surface.pixelArray[++offset] = colour & 0xFF;
    },

    line: function(x1, y1, x2, y2, colour) {
        var dx = (x2 - x1 < 0) ? x1 - x2 : x2 - x1; // Absolute
        var dy = (y2 - y1 < 0) ? y1 - y2 : y2 - y1;

        if (dx < dy) {
            dx = (x2 - x1 << 16) / dy | 0; // Cast float to int
            dy = (y1 < y2) ? 65536 : -65536;
        } else {
            dy = (y2 - y1 << 16) / dx | 0;
            dx = (x1 < x2) ? 65536 : -65536;
        }

        x1 = (x1 << 16) + 32768; // Round from fixed-point
        y1 = (y1 << 16) + 32768;

        while (true) {
            Surface.plot(x1 >> 16, y1 >> 16, colour);

            if (x1 >> 16 == x2 && y1 >> 16 == y2) break;

            x1 += dx;
            y1 += dy;
        }
    },

    rect: function(x, y, w, h, colour) {
        if (w < 0) {
            var x1 = ++x + w;
            var x2 = x;
        } else {
            var x1 = x;
            var x2 = x + w;
        }
        if (h < 0) {
            var y1 = ++y + h;
            var y2 = y;
        } else {
            var y1 = y;
            var y2 = y + h;
        }

        for (y = y1; y < y2; y++) {
            for (x = x1; x < x2; x++) {
                Surface.plot(x, y, colour);
            }
        }
    },

    load: function(source, resource, transparent) {
        var image = new Image();

        image.onload = function() {
            var canvas = document.createElement('canvas');
            canvas.width = image.width;
            canvas.height = image.height;
            var context = canvas.getContext('2d');
            context.drawImage(image, 0, 0);
            var data = context.getImageData(0, 0, image.width, image.height).data;

            for (var i = image.width * image.height; i--;) {
                resource[2 + i] = data[(i << 2) + 2] | data[(i << 2) + 1] << 8 | data[i << 2] << 16;
            }
            resource[0] = image.width;
            resource[1] = transparent;
        }

        image.src = source;
    },

    texel: function(source, x, y) {
        return source[2 + y * source[0] + x];
    },

    blit: function(source, sx, sy, dx, dy, w, h, mask) {
        mask = mask || 0xFFFFFF; // Optional colour mask

        // Clip
        if (dx < 0) {
            var x1 = 0;
            sx -= dx;
        } else {
            var x1 = dx;
        }
        if (dy < 0) {
            var y1 = 0;
            sy -= dy;
        } else {
            var y1 = dy;
        }
        var x2 = (dx + w < Surface.width) ? dx + w : Surface.width;
        var y2 = (dy + h < Surface.height) ? dy + h : Surface.height;

        for (var x, y = y1, u, v = sy, offset; y < y2; y++, v++) {
            offset = 2 + v * source[0];

            for (x = x1, u = sx; x < x2; x++, u++) {
                source[offset + u] == source[1] || Surface.plot(x, y, source[offset + u] & mask);
            }
        }
    },

    scale: function(source, sx, sy, sw, sh, dx, dy, dw, dh, mask) {
        mask = mask || 0xFFFFFF;

        // Scale
        sw = (sw << 16) / (dw - 1) - 1 | 0; // Fix rounding errors by subtracting
        sh = (sh << 16) / (dh - 1) - 1 | 0;

        if (dx < 0) {
            var x1 = 0;
            sx = (sx << 16) + sw * -dx;
        } else {
            var x1 = dx;
            sx = sx << 16;
        }
        if (dy < 0) {
            var y1 = 0;
            sy = (sy << 16) + sh * -dy;
        } else {
            var y1 = dy;
            sy = sy << 16;
        }
        var x2 = (dx + dw < Surface.width) ? dx + dw : Surface.width;
        var y2 = (dy + dh < Surface.height) ? dy + dh : Surface.height;

        for (var x, y = y1, u, v = sy, offset; y < y2; y++, v += sh) {
            offset = 2 + (v >> 16) * source[0];

            for (x = x1, u = sx; x < x2; x++, u += sw) {
                source[offset + (u >> 16)] == source[1] || Surface.plot(x, y, source[offset + (u >> 16)] & mask);
            }
        }
    },

    key: [],

    keyDown: function(e) {
        Surface.key[(e || event).keyCode] = true;
    },

    keyUp: function(e) {
        Surface.key[(e || event).keyCode] = false;
    },

    mouse: [],

    mouseMove: function(e) {
        // Map coordinates to canvas relative of size
        Surface.mouse.x = ((e || event).pageX - Surface.canvas.offsetLeft - Surface.canvas.clientLeft) * Surface.width / Surface.canvas.clientWidth | 0;
        Surface.mouse.y = ((e || event).pageY - Surface.canvas.offsetTop - Surface.canvas.clientTop) * Surface.height / Surface.canvas.clientHeight | 0;
    },

    mouseDown: function(e) {
        Surface.mouse[(e || event).button] = true;
    },

    mouseUp: function(e) {
        Surface.mouse[(e || event).button] = false;
    },

    touch: [],

    touchMove: function(e) {
        (e || event).preventDefault();

        Surface.touch.x = ((e || event).targetTouches[0].pageX - Surface.canvas.offsetLeft - Surface.canvas.clientLeft) * Surface.width / Surface.canvas.clientWidth | 0;
        Surface.touch.y = ((e || event).targetTouches[0].pageY - Surface.canvas.offsetTop - Surface.canvas.clientTop) * Surface.height / Surface.canvas.clientHeight | 0;
    },

    touchStart: function(e) {
        Surface.touch.left = true;
        Surface.touchMove(e);
    },

    touchEnd: function() {
        Surface.touch.left = false;
    },

    cursor: function() {
        return (Surface.touch.left !== undefined) ? Surface.touch : Surface.mouse;
    }

}

Surface.mouse.x = 0;
Surface.mouse.y = 0;

addEventListener('keydown', Surface.keyDown, false);
addEventListener('keyup', Surface.keyUp, false);

addEventListener('mousemove', Surface.mouseMove, false);
addEventListener('mousedown', Surface.mouseDown, false);
addEventListener('mouseup', Surface.mouseUp, false);

addEventListener('touchmove', Surface.touchMove, true);
addEventListener('touchstart', Surface.touchStart, false);
addEventListener('touchend', Surface.touchEnd, false);
