/**
 * @source https://www.chartjs.org/docs/latest/samples/utils.html
 */

const MONTHS = [
    'January',
    'February',
    'March',
    'April',
    'May',
    'June',
    'July',
    'August',
    'September',
    'October',
    'November',
    'December'
];

function months(config) {
    let cfg = config || {};
    let count = cfg.count || 12;
    let section = cfg.section;
    let values = [];
    let value, i;

    for(i = 0; i < count; ++i) {
        value = MONTHS[Math.ceil(i) % 12];
        values.push(value.substring(0, section));
    }

    return values;
}

const COLORS = [
    '#4dc9f6',
    '#f67019',
    '#f53794',
    '#537bc4',
    '#acc236',
    '#166a8f',
    '#00a950',
    '#58595b',
    '#8549ba'
  ];
  
function color(index) {
    return COLORS[index % COLORS.length];
}

const CHART_COLORS = {
    red: 'rgb(255, 99, 132)',
    orange: 'rgb(255, 159, 64)',
    yellow: 'rgb(255, 205, 86)',
    green: 'rgb(75, 192, 192)',
    blue: 'rgb(54, 162, 235)',
    purple: 'rgb(153, 102, 255)',
    grey: 'rgb(201, 203, 207)'
};

const CHART_COLORS_TRANSPARENT = {
    red: 'rgba(255, 99, 132, 0.9)',
    orange: 'rgba(255, 159, 64, 0.9)',
    yellow: 'rgba(255, 205, 86, 0.9)',
    green: 'rgba(75, 192, 192, 0.9)',
    blue: 'rgba(54, 162, 235, 0.9)',
    purple: 'rgba(153, 102, 255, 0.9)',
    grey: 'rgba(201, 203, 207, 0.9)'
};

const NAMED_COLORS = [
    CHART_COLORS.red,
    CHART_COLORS.orange,
    CHART_COLORS.yellow,
    CHART_COLORS.green,
    CHART_COLORS.blue,
    CHART_COLORS.purple,
    CHART_COLORS.grey,
];
  
function namedColor(index) {
    return NAMED_COLORS[index % NAMED_COLORS.length];
}

function generateRandomColor() {
    let letters = '0123456789ABCDEF'.split('');
    let color = '#';

    for (let i = 0; i < 6; i++ ) 
        color += letters[Math.floor(Math.random() * 16)];
        
    return color;
}

// Source: https://stackoverflow.com/a/20114631
function colorLuminance(hex, lum) {
    hex = String(hex).replace(/[^0-9a-f]/gi, '');

    if (hex.length < 6) 
      hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
    
    lum = lum || 0;
    
    let rgb = "#", c, i;
    for (i = 0; i < 3; i++) {
      c = parseInt(hex.substr(i*2,2), 16);
      c = Math.round(Math.min(Math.max(0, c + (c * lum)), 255)).toString(16);
      rgb += ("00"+c).substr(c.length);
    }
  
    return rgb;
}

function hexToRGB(hex) {
    let result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);

    /* return result ? {
      r: parseInt(result[1], 16),
      g: parseInt(result[2], 16),
      b: parseInt(result[3], 16)
    } : null; */

    return result ? `rgb(${parseInt(result[1], 16)}, ${parseInt(result[2], 16)}, ${parseInt(result[3], 16)})` : null;
}

function transparentizeRGBColor(rgb) {
    let result = /^rgb\([\d]+\,\s?[\d]+\,\s?[\d]+\)/i.exec(rgb);

    rgb = rgb.replace('rgb', 'rgba');
    return result ? `${rgb.substring(0, rgb.length - 1)}, 0.9)` : null;
}