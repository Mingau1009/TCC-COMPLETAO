<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">


    <title>Antes e Depois de Imagens</title>

    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include("../Classe/Conexao.php") ?>

<?php $aluno_id = isset($_GET["aluno_id"]) ? $_GET["aluno_id"] : NULL; ?>
    <br>
    <tbody>

    <?php $aluno = Db::conexao()->query("SELECT * FROM `aluno` WHERE `id` = {$aluno_id}")->fetch(PDO::FETCH_OBJ);?>

    </tbody>
    <div class="container">
        <?php if($aluno) { ?>
            <h2>Evolução do Aluno: <?php echo $aluno->nome; ?></h2>
        <?php } else { ?>
            <h2>Evolução do Aluno</h2>
        <?php }  ?>
               
        <div class="image-comparison">
            <div class="preview-img before">
                <h3>Antes</h3>
                <img src="image-placeholder.svg" alt="" id="beforeImg">
                <input type="date" id="dateBefore" class="form-control mb-2">
                <input type="file" id="beforeInput" accept="image/*" class="form-control">
            </div>
            
            <br>
            <div class="preview-img after">
                <h3>Depois</h3>
                <img src="image-placeholder.svg" alt="" id="afterImg">
                <input type="date" id="dateAfter" class="form-control mb-2">
                <input type="file" id="afterInput" accept="image/*" class="form-control">
            </div>
            <br>
        </div>
        

        <div class="controls">
            <br>
            <br>
            <button id="uploadBtn">Carregar Imagens</button>
            <br>
        </div>

        <div class="pdf-controls">
            <button id="resetBtn">Redefinir Imagens</button>
            <button id="view-pdf">Visualizar PDF</button>
            <button id="generate-pdf">Gerar PDF</button>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="script.js"></script>
    <script>
        // Função para formatar data em português
        function formatarData(data) {
            if (!data) return "Sem data";
            const [ano, mes, dia] = data.split('-');
            const dataObj = new Date(ano, mes - 1, dia);
            return dataObj.toLocaleDateString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        }

        // Função para gerar o PDF com as imagens
        document.getElementById('generate-pdf').addEventListener('click', async () => {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            const images = Array.from(document.querySelectorAll('img'));
            const dates = [
                document.getElementById('dateBefore').value,
                document.getElementById('dateAfter').value
            ];

            const imgWidth = 75; 
            const imgHeight = 75; 
            const x1 = 5; 
            const x2 = 100; 
            let y = 10; 

            for (let i = 0; i < images.length; i++) {
                const img = images[i].src;
                let label = ""; 
                let xPosition = x1; 

                if (i === 0) {
                    label = "Antes: " + formatarData(dates[i]);
                    xPosition = x1; 
                } else if (i === 1) {
                    label = "Depois: " + formatarData(dates[i]);
                    xPosition = x2; 
                }

                if (label) {
                    doc.text(label, xPosition, y);
                }

                const imgData = await fetch(img)
                    .then(res => res.blob())
                    .then(blob => {
                        return new Promise((resolve) => {
                            const reader = new FileReader();
                            reader.onloadend = () => resolve(reader.result);
                            reader.readAsDataURL(blob);
                        });
                    });

                doc.addImage(imgData, 'JPEG', xPosition, y + 10, imgWidth, imgHeight);
            }

            doc.save('imagens_com_datas.pdf');
        });

        // Função para visualizar o PDF gerado em uma nova aba
        document.getElementById('view-pdf').addEventListener('click', async () => {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            const images = Array.from(document.querySelectorAll('img'));
            const dates = [
                document.getElementById('dateBefore').value,
                document.getElementById('dateAfter').value
            ];

            const imgWidth = 75; 
            const imgHeight = 75; 
            const x1 = 5; 
            const x2 = 100; 
            let y = 10; 

            for (let i = 0; i < images.length; i++) {
                const img = images[i].src;
                let label = ""; 
                let xPosition = x1; 

                if (i === 0) {
                    label = "Antes: " + formatarData(dates[i]);
                    xPosition = x1; 
                } else if (i === 1) {
                    label = "Depois: " + formatarData(dates[i]);
                    xPosition = x2; 
                }

                if (label) {
                    doc.text(label, xPosition, y);
                }

                const imgData = await fetch(img)
                    .then(res => res.blob())
                    .then(blob => {
                        return new Promise((resolve) => {
                            const reader = new FileReader();
                            reader.onloadend = () => resolve(reader.result);
                            reader.readAsDataURL(blob);
                        });
                    });

                doc.addImage(imgData, 'JPEG', xPosition, y + 10, imgWidth, imgHeight);
            }

            const pdfData = doc.output('blob');
            const pdfUrl = URL.createObjectURL(pdfData);
            window.open(pdfUrl, '_blank');
        });

        // Função para redefinir as imagens e campos de entrada para o valor inicial
        document.getElementById('resetBtn').addEventListener('click', () => {
            document.getElementById('beforeImg').src = 'image-placeholder.svg';
            document.getElementById('afterImg').src = 'image-placeholder.svg';
            document.getElementById('beforeInput').value = '';
            document.getElementById('afterInput').value = '';
            document.getElementById('dateBefore').value = '';
            document.getElementById('dateAfter').value = '';
        });
    </script>
</body>
</html>