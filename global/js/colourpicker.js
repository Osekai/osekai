function componentToHex(c) {
    const hex = c.toString(16);
    return hex.length == 1 ? "0" + hex : hex;
}

function rgbToHex(r, g, b) {
    return "#" + componentToHex(parseInt(r)) + componentToHex(parseInt(g)) + componentToHex(parseInt(b));
}

function hexToRgb(hex) {
    const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}

function newColourBar(id, changeCallback, col1 = null, col2 = null) {
    let gradient_1_col = [255, 0, 0];
    let gradient_2_col = [0, 0, 255];

    const element = document.getElementById(id);
    const bar_left = element.getElementsByClassName("osekai__gradient-bar-left")[0];
    const bar_right = element.getElementsByClassName("osekai__gradient-bar-right")[0];
    const bar_bar = element.getElementsByClassName("osekai__gradient-bar-bar")[0];

    const bar_left_input = bar_left.getElementsByTagName("input")[0];
    const bar_right_input = bar_right.getElementsByTagName("input")[0];

    function updateUI(changeInputs = false) {
        const gradient_1_col_hex = rgbToHex(gradient_1_col[0], gradient_1_col[1], gradient_1_col[2]);
        const gradient_2_col_hex = rgbToHex(gradient_2_col[0], gradient_2_col[1], gradient_2_col[2]);
        console.log(gradient_1_col_hex);
        console.log(gradient_2_col_hex);
        bar_left.style.background = gradient_1_col_hex;
        bar_right.style.background = gradient_2_col_hex;
        if (changeInputs) {
            bar_left_input.value = gradient_1_col_hex;
            bar_right_input.value = gradient_2_col_hex;
            // picker1.enter();
            // picker1.exit();
            // picker2.enter();
            // picker2.exit();
        }
        bar_bar.style.background = "linear-gradient(to right, " + gradient_1_col_hex + ", " + gradient_2_col_hex + ")";
    }

    const picker1 = new CP(bar_left_input);
    const picker2 = new CP(bar_right_input);


    const resp = {
        setColour: function (col1, col2) {
            gradient_1_col = col1;
            gradient_2_col = col2;
            updateUI(true);
        },
    };


    picker1.on('change', function (r, g, b, a) {
        gradient_1_col = [r, g, b];
        this.source.value = rgbToHex(r, g, b);
        updateUI();
        if (changeCallback) {
            changeCallback(gradient_1_col, gradient_2_col);
        }
    });
    picker2.on('change', function (r, g, b, a) {
        gradient_2_col = [r, g, b];
        this.source.value = rgbToHex(r, g, b);
        updateUI();
        if (changeCallback) {
            changeCallback(gradient_1_col, gradient_2_col);
        }
    });

    bar_left_input.addEventListener("change", function () {
        const hex = this.value;
        const rgb = hexToRgb(hex);
        if (rgb) {
            gradient_1_col = [rgb.r, rgb.g, rgb.b];
            picker1.exit();
            picker1.enter();
            updateUI();
            if (changeCallback) {
                changeCallback(gradient_1_col, gradient_2_col);
            }
        }
    });
    bar_right_input.addEventListener("change", function () {
        const hex = this.value;
        const rgb = hexToRgb(hex);
        if (rgb) {
            gradient_2_col = [rgb.r, rgb.g, rgb.b];
            picker2.exit();
            picker2.enter();
            updateUI();
            if (changeCallback) {
                changeCallback(gradient_1_col, gradient_2_col);
            }
        }
    });

    if (col1 != null && col2 != null) {
        gradient_1_col = col1;
        gradient_2_col = col2;
        bar_left_input.value = rgbToHex(gradient_1_col[0], gradient_1_col[1], gradient_1_col[2]);
        bar_right_input.value = rgbToHex(gradient_2_col[0], gradient_2_col[1], gradient_2_col[2]);
        updateUI();
    }

    return resp;
}


function newColourPicker(id, changeCallback, col1 = null) {
    const element = document.getElementById(id);
    const input = element.getElementsByTagName("input")[0];

    let col = [255, 0, 0];

    function updateUI(changeInputs = false) {
        const col_hex = rgbToHex(col[0], col[1], col[2]);
        console.log(col_hex);
        element.style.background = col_hex;
        if (changeInputs) {
            input.value = col_hex;
            // picker1.enter();
            // picker1.exit();
            // picker2.enter();
            // picker2.exit();
        }
    }


    const picker1 = new CP(input);

    const resp = {
        setColour: function (col1, col2) {
            col = col1;
            updateUI(true);
        },
    };
    let firstTime = true;

    picker1.on('change', function (r, g, b, a) {
        if (firstTime) {
            // dumb workaround to it setting to #000000 at loadtime
            firstTime = false;
            return;
        }
        col = [r, g, b];
        console.log("CP updating with colour " + col);
        this.source.value = rgbToHex(r, g, b);
        updateUI();
        if (changeCallback) {
            changeCallback(col);
        }
    });


    input.addEventListener("change", function () {
        const hex = this.value;
        const rgb = hexToRgb(hex);
        console.log("CP input changing with colour " + rgb);
        if (rgb) {
            col = [rgb.r, rgb.g, rgb.b];
            picker1.exit();
            picker1.enter();
            updateUI();
            if (changeCallback) {
                changeCallback(col);
            }
        }
    });


    if (col1 != null) {
        console.log("loading CP with default of " + col1);
        col = col1;
        input.value = rgbToHex(col[0], col[1], col[2]);
        updateUI();
    }

    return resp;
}
