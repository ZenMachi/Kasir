<?php

//session
session_start();


//koneksi
$c = mysqli_connect('localhost','root','','kasir');

if($c){
    echo 'Berhasil';
}

//login
if(isset($_POST['login'])){
    //initiate variable
    $username = $_POST['username'];
    $password = $_POST['password'];

    $check = mysqli_query($c,"SELECT * FROM user WHERE username='$username' and password='$password' ");
    $hitung = mysqli_num_rows($check);

    if($hitung>0){
        //Jika data ditemukan
        //Berhasil login

        $_SESSION['login'] = 'True';
        header('location:index.php');
    } else {
        //Data tidak ditemukan
        //Gagal Login
        echo '
        <script>alert("Username atau Password salah");
        window.location.href="login.php"
        </script>
        ';
    }
}

//tambah barang
if(isset($_POST['tambahbarang'])){
    $namaproduk = $_POST['namaproduk'];
    $deskripsi = $_POST['deskripsi'];
    $stock = $_POST['stock'];
    $harga = $_POST['harga'];

    $insert = mysqli_query($c,"INSERT INTO produk (namaproduk, deskripsi, harga, stock) VALUES ('$namaproduk', '$deskripsi', '$harga', '$stock')" );
                
    if($insert){
        header('location:stock.php');
    } else {
        echo '
        <script>alert("Gagal Menambah barang baru");
        window.location.href="stock.php"
        </script>
        ';
    }

}

//tambah pelanggan
if(isset($_POST['tambahpelanggan'])){
    $namapelanggan = $_POST['namapelanggan'];
    $notelp = $_POST['notelp'];
    $alamat = $_POST['alamat'];

    $insert = mysqli_query($c,"INSERT INTO pelanggan (namapelanggan, notelp, alamat) VALUES ('$namapelanggan', '$notelp', '$alamat')" );
                
    if($insert){
        header('location:pelanggan.php');
    } else {
        echo '
        <script>alert("Gagal Menambah Pelanggan baru");
        window.location.href="pelanggan.php"
        </script>
        ';
    }

}

//tambah pesanan
if(isset($_POST['tambahpesanan'])){
    $idpelanggan = $_POST['idpelanggan'];

    $insert = mysqli_query($c,"INSERT INTO pesanan (idpelanggan) VALUES ('$idpelanggan')" );
                
    if($insert){
        header('location:index.php');
    } else {
        echo '
        <script>alert("Gagal Menambah Pesanan baru");
        window.location.href="index.php"
        </script>
        ';
    }

}


//tambah produk
if(isset($_POST['addproduk'])){
    $idproduk = $_POST['idproduk'];
    $idp = $_POST['idp']; //idpesanan
    $qty = $_POST['qty']; //jumlah

    
                
    //hitung stock sekarang ada berapa

    $hitung1 = mysqli_query($c,"select * from produk where idproduk='$idproduk'");
    $hitung2 = mysqli_fetch_array($hitung1);
    $stocksekarang = $hitung2['stock']; //stock barang saat ini

    if($stocksekarang>=$qty){

        // kurangi stocknya dengan jumlah yang akan dikeluarkan
        $selisih = $stocksekarang-$qty;

        //stock cukup
        $insert = mysqli_query($c,"INSERT INTO detailpesanan (idpesanan, idproduk, qty) VALUES ('$idp', '$idproduk', '$qty' )" );
        $update = mysqli_query($c,"update produk set stock='$selisih' where idproduk='$idproduk'" );

        if($insert && $update){
            header('location:view.php?idp='.$idp);
        } else {
            echo '
            <script>alert("Gagal Menambah Pesanan baru");
            window.location.href="view.php?idp='.$idp.'"
            </script>
            ';
        }
    } else {
        echo '
        <script>alert("Stock barang tidak cukup");
        window.location.href="view.php?idp='.$idp.'"
        </script>
        ';

    }

}


//Menambah Barang Masuk
if(isset($_POST['barangmasuk'])){
    $idproduk = $_POST['idproduk'];
    $qty = $_POST['qty'];


     //cari tahu stock sekarang berapa
     $caristock = mysqli_query($c,"select * from produk where idproduk='$idproduk'");
     $caristock2 = mysqli_fetch_array($caristock);
     $stocksekarang = $caristock2['stock'];

     //hitung
     $newstock = $stocksekarang+$qty;


    $insertb = mysqli_query($c,"insert into masuk (idproduk,qty) values ('$idproduk','$qty')");
    $updatetb = mysqli_query($c,"update produk set stock='$newstock' where idproduk='$idproduk'");


    if($insertb && $updatetb){
        header('location:masuk.php');

    } else {
        echo '
        <script>alert("Gagal");
        window.location.href="masuk.php"
        </script>
        ';

    }
}



//hapus produk pesanan
if(isset($_POST['hapusprodukpesanan'])){
    $idp = $_POST['idp'];
    $idpr = $_POST['idpr'];
    $idpesanan = $_POST['idpesanan'];

    //cek qty sekarang
    $cek1 = mysqli_query($c,"select * from detailpesanan where iddetailpesanan='$idp'");
    $cek2 = mysqli_fetch_array($cek1);
    $qtysekarang = $cek2['qty'];

    //Cek stock sekarang
    $cek3 = mysqli_query($c,"select * from produk where idproduk='$idpr'");
    $cek4 = mysqli_fetch_array($cek3);
    $stocksekarang = $cek4['stock'];


    $hitung = $stocksekarang+$qtysekarang;

    $update = mysqli_query($c,"update produk set stock='$hitung' where idproduk='$idpr'"); //update stock
    $hapus = mysqli_query($c, "delete from detailpesanan where idproduk='$idpr' and iddetailpesanan='$idp'");

    if($update && $hapus){
        header('location:view.php?idp='.$idpesanan);

    } else {
        echo '
            <script>alert("Gagal Menghapus Barang");
            window.location.href="view.php?idp='.$idpesanan.'"
            </script>
            ';

    }

}


//Edit Barang
if(isset($_POST['editbarang'])){
    $np = $_POST['namaproduk'];
    $desc = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $idp = $_POST['idp']; //idproduk

    $query = mysqli_query($c,"update produk set namaproduk='$np', deskripsi='$desc', harga='$harga' where idproduk='$idp'");

    if($query){
        header('location:stock.php');
    } else {
        echo '
        <script>alert("Gagal");
        window.location.href="stock.php"
        </script>
        ';

    }

}

//hapus barang
if(isset($_POST['hapusbarang'])){
    $idp = $_POST['idp'];

    $query = mysqli_query($c,"delete from produk where idproduk='$idp'");

    
    if($query){
        header('location:stock.php');
    } else {
        echo '
        <script>alert("Gagal");
        window.location.href="stock.php"
        </script>
        ';

    }
}

//Edit Pelanggan
if(isset($_POST['editpelanggan'])){
    $np = $_POST['namapelanggan'];
    $nt = $_POST['notelp'];
    $a = $_POST['alamat'];
    $id = $_POST['idpl']; //idproduk

    $query = mysqli_query($c,"update pelanggan set namapelanggan='$np', notelp='$nt', alamat='$a' where idpelanggan='$id'");

    if($query){
        header('location:pelanggan.php');
    } else {
        echo '
        <script>alert("Gagal");
        window.location.href="pelanggan.php"
        </script>
        ';

    }

}




//hapus Pelanggan
if(isset($_POST['hapuspelanggan'])){
    $idp = $_POST['idpl'];

    $query = mysqli_query($c,"delete from pelanggan where idpelanggan='$idp'");

    
    if($query){
        header('location:pelanggan.php');
    } else {
        echo '
        <script>alert("Gagal");
        window.location.href="pelanggan.php"
        </script>
        ';

    }
}


//mengubah data barang masuk
if(isset($_POST['editdatabarangmasuk'])){
    $qty = $_POST['qty'];
    $idm = $_POST['idm'];//id masuk
    $idp = $_POST['idp'];//id produk

    

    //cari tau qty nya sekarang brapa
    $caritahu = mysqli_query($c,"select * from masuk where idmasuk='$idm'");
    $caritahu2 = mysqli_fetch_array($caritahu);
    $qtysekarang = $caritahu2['qty'];
    
    //cari tahu stock sekarang berapa
    $caristock = mysqli_query($c,"select * from produk where idproduk='$idp'");
    $caristock2 = mysqli_fetch_array($caristock);
    $stocksekarang = $caristock2['stock'];

    if($qty >= $qtysekarang){
        //kalau inputan user lebih besarr daripada qty yg tercatat
        //hitung selisih
        $selisih = $qty-$qtysekarang;
        $newstock = $stocksekarang+$selisih;

        $query1 = mysqli_query($c,"update masuk set qty='$qty' where idmasuk='$idm'");
        $query2 = mysqli_query($c,"update produk set stock='$newstock' where idproduk='$idp'");
        
        

        if($query1 && $query2){
            header('location:masuk.php');
        } else {
            echo '
            <script>alert("Gagal");
            window.location.href="masuk.php"
            </script>
            ';

        }

    } else {
        //kalau lebih kecil
        //hitung selisih
        $selisih = $qtysekarang-$qty;
        $newstock = $stocksekarang-$selisih;

        $query1 = mysqli_query($c,"update masuk set qty='$qty' where idmasuk='$idm'");
        $query2 = mysqli_query($c,"update produk set stock='$newstock' where idproduk='$idp'");
        
        

        if($query1 && $query2){
            header('location:masuk.php');
        } else {
            echo '
            <script>alert("Gagal");
            window.location.href="masuk.php"
            </script>
            ';

        }

    }



    

}


//hapus data barang masuk
if(isset($_POST['hapusdatabarangmasuk'])){
    $idp = $_POST['idp'];
    $idm = $_POST['idm'];

     //cari tau qty nya sekarang brapa
     $caritahu = mysqli_query($c,"select * from masuk where idmasuk='$idm'");
     $caritahu2 = mysqli_fetch_array($caritahu);
     $qtysekarang = $caritahu2['qty'];
     
     //cari tahu stock sekarang berapa
     $caristock = mysqli_query($c,"select * from produk where idproduk='$idp'");
     $caristock2 = mysqli_fetch_array($caristock);
     $stocksekarang = $caristock2['stock'];



        //hitung selisih
        $newstock = $stocksekarang-$qtysekarang;

        $query1 = mysqli_query($c,"delete from masuk where idmasuk='$idm'");
        $query2 = mysqli_query($c,"update produk set stock='$newstock' where idproduk='$idp'");
        
        

        if($query1 && $query2){
            header('location:masuk.php');
        } else {
            echo '
            <script>alert("Gagal");
            window.location.href="masuk.php"
            </script>
            ';

        }

//     $query = mysqli_query($c,"delete from pelanggan where idpelanggan='$idp'");

    
//     if($query){
//         header('location:pelanggan.php');
//     } else {
//         echo '
//         <script>alert("Gagal");
//         window.location.href="pelanggan.php"
//         </script>
//         ';

//     }
}


//hapus Pelanggan
if(isset($_POST['hapusorder'])){
    $idps = $_POST['idps'];

    $cekdata = mysqli_query($c,"select * from detailpesanan dp where idpesanan='$idps'");
    while($ok=mysqli_fetch_array($cekdata)){
        //balikin stok
        $qty = $ok['$qty'];
        $idproduk = $ok['$idproduk'];
        $iddp = $ok['iddetailpesanan'];

         //cari tahu stock sekarang berapa
     $caristock = mysqli_query($c,"select * from produk where idproduk='$idproduk'");
     $caristock2 = mysqli_fetch_array($caristock);
     $stocksekarang = $caristock2['stock'];

     $newstock = $stocksekarang+$qty;

     $queryupdate = mysqli_query($c,"update produk set stock='$newstock' where idproduk='$idproduk'");
        


        //hapus data

        $querydelete = mysqli_query($c,"delete from detailpesanan where iddetailpesanan='$iddp'");


    
    }

    $query = mysqli_query($c,"delete from pesanan where idpesanan='$idps'");

    
    if($queryupdate && $querydelete && $query){
        header('location:index.php');
    } else {
        echo '
        <script>alert("Gagal");
        window.location.href="index.php"
        </script>
        ';

    }
}

//mengubah data detail pesanan
if(isset($_POST['editdetailpesanan'])){
    $qty = $_POST['qty'];
    $iddp = $_POST['iddp'];//id masuk
    $idpr = $_POST['idpr'];//id produk
    $idp = $_POST['$idp'];//id pesanan

    

    //cari tau qty nya sekarang brapa
    $caritahu = mysqli_query($c,"select * from detailpesanan where iddetailpesanan='$iddp'");
    $caritahu2 = mysqli_fetch_array($caritahu);
    $qtysekarang = $caritahu2['qty'];
    
    //cari tahu stock sekarang berapa
    $caristock = mysqli_query($c,"select * from produk where idproduk='$idpr'");
    $caristock2 = mysqli_fetch_array($caristock);
    $stocksekarang = $caristock2['stock'];

    if($qty >= $qtysekarang){
        //kalau inputan user lebih besarr daripada qty yg tercatat
        //hitung selisih
        $selisih = $qty-$qtysekarang;
        $newstock = $stocksekarang-$selisih;

        $query1 = mysqli_query($c,"update detailpesanan set qty='$qty' where iddetailpesanan='$iddp'");
        $query2 = mysqli_query($c,"update produk set stock='$newstock' where idproduk='$idpr'");
        
        

        if($query1 && $query2){
            header('location:view.php?idp='.$idp);
        } else {
            echo '
            <script>alert("Gagal");
            window.location.href="view.php?idp='.$idp.'"
            </script>
            ';

        }

    } else {
        //kalau lebih kecil
        //hitung selisih
        $selisih = $qtysekarang-$qty;
        $newstock = $stocksekarang+$selisih;

        $query1 = mysqli_query($c,"update detailpesanan set qty='$qty' where iddetailpesanan='$iddp'");
        $query2 = mysqli_query($c,"update produk set stock='$newstock' where idproduk='$idpr'");
        
        

        if($query1 && $query2){
            header('location:view.php?idp='.$idp);
        } else {
            echo '
            <script>alert("Gagal");
            window.location.href="view.php?idp='.$idp.'"
            </script>
            ';

        }

    }



    

}



?>