const filepicker = document.getElementById('file');

filepicker.addEventListener('change', (event) => {
    const files = event.target.files;


    for (const file of files) {
        document.getElementById('preview').src = window.URL.createObjectURL(file)
    }
});