<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Gambar ke PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
        }

        h1 {
            text-align: center;
            padding: 20px;
            background-color: #4CAF50;
            color: white;
            margin: 0;
        }

        form {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
        }

        input[type="file"] {
            width: 100%;
            margin-bottom: 20px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #45a049;
        }

        #pdf-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 10px;
            background: white;
            border: 2px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            height: 600px;
            position: relative;
        }

        .draggable-image {
            position: absolute;
            cursor: grab;
            z-index: 10;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 2px dashed #4CAF50;
            border-radius: 5px;
        }

        .resize-handle {
            width: 10px;
            height: 10px;
            background: #4CAF50;
            position: absolute;
            bottom: 0;
            right: 0;
            cursor: nwse-resize;
        }

        #save-button, #download-button {
            display: none;
            margin: 20px auto;
            display: block;
            width: 200px;
            text-align: center;
        }
    </style>
</head>
<body>

    <h1>Tambah Gambar ke PDF</h1>
    <form id="upload-form">
        <label for="pdf_file">Unggah PDF:</label>
        <input type="file" id="pdf_file" accept=".pdf" required>

        <label for="image_file">Unggah Gambar:</label>
        <input type="file" id="image_file" accept="image/*" required>

        <button type="button" onclick="processFiles()">Proses PDF</button>
    </form>

    <div id="pdf-container"></div>
    <button id="save-button" onclick="saveChanges()">Simpan Perubahan</button>
    <button id="download-button" onclick="downloadPDF()">Download PDF</button>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        let pdfDoc = null;
        let currentPage = 1;
        let imageElement = null;
        let imagePosition = { top: 100, left: 100, width: 100, height: 100 };
        let isDragging = false;
        let isResizing = false;
        let offsetX, offsetY;

        // Load PDF
        function loadPDF(pdfData) {
            const loadingTask = pdfjsLib.getDocument({ data: pdfData });
            loadingTask.promise.then(doc => {
                pdfDoc = doc;
                renderPage(currentPage);
            });
        }

        // Render PDF Page
        function renderPage(pageNum) {
            pdfDoc.getPage(pageNum).then(page => {
                const viewport = page.getViewport({ scale: 1.5 });
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                page.render({
                    canvasContext: ctx,
                    viewport: viewport
                }).promise.then(() => {
                    const container = document.getElementById('pdf-container');
                    container.innerHTML = ''; // Clear previous pages
                    container.appendChild(canvas);
                    if (imageElement) container.appendChild(imageElement);
                });
            });
        }

        // Handle PDF Upload
        document.getElementById('pdf_file').addEventListener('change', function (event) {
            const file = event.target.files[0];
            const reader = new FileReader();
            reader.onload = e => loadPDF(e.target.result);
            reader.readAsArrayBuffer(file);
        });

        // Handle Image Upload
        document.getElementById('image_file').addEventListener('change', function (event) {
            const file = event.target.files[0];
            const reader = new FileReader();
            reader.onload = e => {
                if (imageElement) imageElement.remove();

                imageElement = document.createElement('img');
                imageElement.src = e.target.result;
                imageElement.classList.add('draggable-image');
                imageElement.style.width = `${imagePosition.width}px`;
                imageElement.style.height = `${imagePosition.height}px`;
                imageElement.style.left = `${imagePosition.left}px`;
                imageElement.style.top = `${imagePosition.top}px`;

                const resizeHandle = document.createElement('div');
                resizeHandle.classList.add('resize-handle');
                imageElement.appendChild(resizeHandle);

                const container = document.getElementById('pdf-container');
                container.appendChild(imageElement);

                addDragAndResize(imageElement);
            };
            reader.readAsDataURL(file);
        });

        // Drag and Resize Functions
        function addDragAndResize(image) {
            image.addEventListener('mousedown', e => {
                if (e.target.classList.contains('resize-handle')) return;
                isDragging = true;
                offsetX = e.clientX - image.offsetLeft;
                offsetY = e.clientY - image.offsetTop;
            });

            document.addEventListener('mousemove', e => {
                if (isDragging) {
                    imagePosition.left = e.clientX - offsetX;
                    imagePosition.top = e.clientY - offsetY;
                    image.style.left = `${imagePosition.left}px`;
                    image.style.top = `${imagePosition.top}px`;
                }
            });

            document.addEventListener('mouseup', () => isDragging = false);

            const resizeHandle = image.querySelector('.resize-handle');
            resizeHandle.addEventListener('mousedown', e => {
                e.stopPropagation();
                isResizing = true;
                offsetX = e.clientX;
                offsetY = e.clientY;
            });

            document.addEventListener('mousemove', e => {
                if (isResizing) {
                    const newWidth = imagePosition.width + (e.clientX - offsetX);
                    const newHeight = imagePosition.height + (e.clientY - offsetY);
                    if (newWidth > 20 && newHeight > 20) {
                        imagePosition.width = newWidth;
                        imagePosition.height = newHeight;
                        image.style.width = `${imagePosition.width}px`;
                        image.style.height = `${imagePosition.height}px`;
                        offsetX = e.clientX;
                        offsetY = e.clientY;
                    }
                }
            });

            document.addEventListener('mouseup', () => isResizing = false);
        }

        // Download PDF
        function downloadPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            const canvas = document.querySelector('#pdf-container canvas');

            const imgData = canvas.toDataURL('image/jpeg');
            doc.addImage(imgData, 'JPEG', 0, 0, doc.internal.pageSize.getWidth(), doc.internal.pageSize.getHeight());

            if (imageElement) {
                const imgX = (imagePosition.left / canvas.width) * doc.internal.pageSize.getWidth();
                const imgY = (imagePosition.top / canvas.height) * doc.internal.pageSize.getHeight();
                const imgW = (imagePosition.width / canvas.width) * doc.internal.pageSize.getWidth();
                const imgH = (imagePosition.height / canvas.height) * doc.internal.pageSize.getHeight();
                doc.addImage(imageElement.src, 'PNG', imgX, imgY, imgW, imgH);
            }

            doc.save('modified.pdf');
        }
    </script>
</body>
</html>
