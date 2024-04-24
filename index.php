<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Nilai Mahasiswa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }
        h2 {
            text-align: center;
            margin-top: 20px;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
        .delete-link {
            color: #ff4500;
            cursor: pointer;
        }
        .delete-link:hover {
            text-decoration: underline;
        }
        .update-link {
            color: #28a745;
            cursor: pointer;
        }
        .update-link:hover {
            text-decoration: underline;
        }
        #addForm {
            margin-top: 20px;
            text-align: center;
        }
        #addForm input[type="text"] {
            padding: 8px;
            margin-right: 10px;
        }
        #addForm button {
            padding: 8px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        #addForm button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h2>Daftar Nilai Mahasiswa</h2>
    <table id="nilaiTable">
        <tr>
            <th>NIM</th>
            <th>Nama</th>
            <th>Alamat</th>
            <th>Tanggal Lahir</th>
            <th>Kode MK</th>
            <th>Nama MK</th>
            <th>SKS</th>
            <th>Nilai</th>
            <th>Aksi</th>
        </tr>
    </table>

    <div id="addForm">
        <input type="text" id="nimInput" placeholder="NIM">
        <input type="text" id="kodeMkInput" placeholder="Kode MK">
        <input type="text" id="nilaiInput" placeholder="Nilai">
        <button onclick="addNilai()">Tambah Nilai</button>
    </div>

    <script>
        // Fungsi untuk mengambil data dari API dan memperbarui tabel
        function fetchData() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'http://localhost/uts_psait/nilai_api.php', true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        var data = JSON.parse(xhr.responseText);
                        updateTable(data);
                    } else {
                        console.error('Gagal mengambil data: ' + xhr.status);
                    }
                }
            };
            xhr.send();
        }

        // Fungsi untuk memperbarui tabel dengan data yang diambil dari API
        function updateTable(data) {
            var table = document.getElementById('nilaiTable');
            table.innerHTML = ''; // Kosongkan tabel sebelum memperbarui datanya
            var headerRow = table.insertRow(0);
            var headers = ['NIM', 'Nama', 'Alamat', 'Tanggal Lahir', 'Kode MK', 'Nama MK', 'SKS', 'Nilai', 'Aksi'];
            for (var i = 0; i < headers.length; i++) {
                var headerCell = headerRow.insertCell(i);
                headerCell.textContent = headers[i];
            }
            for (var i = 0; i < data.length; i++) {
                var row = table.insertRow(i + 1);
                var rowData = [
                    data[i].nim,
                    data[i].nama,
                    data[i].alamat,
                    data[i].tanggal_lahir,
                    data[i].kode_mk,
                    data[i].nama_mk,
                    data[i].sks,
                    '<span id="nilai_' + data[i].nim + '_' + data[i].kode_mk + '">' + data[i].nilai + '</span>',
                    '<button onclick="updateNilai(\'' + data[i].nim + '\', \'' + data[i].kode_mk + '\')">Update</button> | ' +
                    '<span class="delete-link" onclick="deleteNilai(\'' + data[i].nim + '\', \'' + data[i].kode_mk + '\')">Delete</span>'
                ];
                for (var j = 0; j < rowData.length; j++) {
                    var cell = row.insertCell(j);
                    cell.innerHTML = rowData[j];
                }
            }
        }

        // Fungsi untuk menambahkan nilai baru
        function addNilai() {
            var nim = document.getElementById('nimInput').value;
            var kodeMk = document.getElementById('kodeMkInput').value;
            var nilai = document.getElementById('nilaiInput').value;
            if (nim.trim() === '' || kodeMk.trim() === '' || nilai.trim() === '') {
                alert('Mohon isi semua kolom.');
                return;
            }
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'http://localhost/uts_psait/nilai_api.php', true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        fetchData(); // Ambil data baru setelah penambahan berhasil
                        document.getElementById('nimInput').value = '';
                        document.getElementById('kodeMkInput').value = '';
                        document.getElementById('nilaiInput').value = '';
                    } else {
                        alert('Gagal menambahkan nilai: ' + xhr.responseText);
                    }
                }
            };
            var requestData = {nim: nim, kode_mk: kodeMk, nilai: nilai};
            xhr.send(JSON.stringify(requestData));
        }

        // Fungsi untuk mengubah nilai
        function updateNilai(nim, kode_mk) {
            var nilaiBaru = prompt('Masukkan nilai baru:');
            if (nilaiBaru !== null) {
                var xhr = new XMLHttpRequest();
                xhr.open('PUT', 'http://localhost/uts_psait/nilai_api.php', true);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            fetchData(); // Ambil data baru setelah perubahan berhasil
                        } else {
                            alert('Gagal mengubah nilai: ' + xhr.responseText);
                        }
                    }
                };
                var requestData = {nim: nim, kode_mk: kode_mk, nilai: nilaiBaru};
                xhr.send(JSON.stringify(requestData));
            }
        }

        // Fungsi untuk menghapus nilai
        function deleteNilai(nim, kode_mk) {
            if (confirm('Apakah Anda yakin ingin menghapus nilai ini?')) {
                var xhr = new XMLHttpRequest();
                xhr.open('DELETE', 'http://localhost/uts_psait/nilai_api.php', true);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            fetchData(); // Ambil data baru setelah penghapusan berhasil
                        } else {
                            alert('Gagal menghapus nilai: ' + xhr.responseText);
                        }
                    }
                };
                xhr.send(JSON.stringify({nim: nim, kode_mk: kode_mk}));
            }
        }

        // Panggil fetchData saat halaman dimuat
        fetchData();
    </script>
</body>
</html>
