useCustomThemeForTool("56, 36, 53", "56, 36, 53");

function calculatePP() {
    let stdpp, taikopp, ctbpp, maniapp;

    if (!isNaN(parseInt(document.getElementById("form-std").value))) {
        document.getElementById("form-std").classList.remove("osekai__input-invalid")
        stdpp = parseInt(document.getElementById("form-std").value);
    }
    else {
        if (document.getElementById("form-std").value == "") {
            document.getElementById("form-std").classList.remove("osekai__input-invalid")
            stdpp = 0;
        }
        else {
            document.getElementById("form-std").classList.add("osekai__input-invalid")
            stdpp = 0;
        }
    }

    if (!isNaN(parseInt(document.getElementById("form-taiko").value))) {
        document.getElementById("form-taiko").classList.remove("osekai__input-invalid")
        taikopp = parseInt(document.getElementById("form-taiko").value);
    }
    else {
        if (document.getElementById("form-taiko").value == "") {
            document.getElementById("form-taiko").classList.remove("osekai__input-invalid")
            taikopp = 0;
        }
        else {
            document.getElementById("form-taiko").classList.add("osekai__input-invalid")
            taikopp = 0;
        }
    }

    if (!isNaN(parseInt(document.getElementById("form-ctb").value))) {
        document.getElementById("form-ctb").classList.remove("osekai__input-invalid")
        ctbpp = parseInt(document.getElementById("form-ctb").value);
    }
    else {
        if (document.getElementById("form-ctb").value == "") {
            document.getElementById("form-ctb").classList.remove("osekai__input-invalid")
            ctbpp = 0;
        }
        else {
            document.getElementById("form-ctb").classList.add("osekai__input-invalid")
            ctbpp = 0;
        }
    }

    if (!isNaN(parseInt(document.getElementById("form-mania").value))) {
        document.getElementById("form-mania").classList.remove("osekai__input-invalid")
        maniapp = parseInt(document.getElementById("form-mania").value);
    }
    else {
        if (document.getElementById("form-mania").value == "") {
            document.getElementById("form-mania").classList.remove("osekai__input-invalid")
            maniapp = 0;
        }
        else {
            document.getElementById("form-mania").classList.add("osekai__input-invalid")
            maniapp = 0;
        }
    }

    let totalpp = stdpp + taikopp + ctbpp + maniapp;
    document.getElementById("result-total").innerHTML = totalpp;

    let mean = totalpp / 4;
    let square = ((stdpp - mean) * (stdpp - mean)) + ((taikopp - mean) * (taikopp - mean)) + ((ctbpp - mean) * (ctbpp - mean)) + ((maniapp - mean) * (maniapp - mean));
    let spp = totalpp - 2 * (Math.sqrt(square / 3));
    document.getElementById("result-spp").innerHTML = Math.round(spp);
}