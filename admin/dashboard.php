<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../assets/css/styleAdmin.css">
</head>
<body>

<div class="parent">

    <div class="sidebar"> 
        <h2>HOTEL SITE</h2> 
<ul class="menu">
    <li class="active">Home</li>

    <li class="dropdown-li">
        Room Management
        <ul class="submenu">
            <li><a href="" class="textstyle"><span >Edit rooms</span></a></li>
            <li><a href="" class="textstyle"><span >Add rooms</span></a></li>
        </ul>
    </li>

    <li class="dropdown-li">
        Payment Management
        <ul class="submenu">
            <li><a href="" class="textstyle"><span >confirm payment</span></a></li>
            <li><a href="" class="textstyle"><span >confirm booking</span></a></li>
        </ul>
    </li>
</ul>

    </div>
    <!-- TOPBAR -->
    <div class="topbar">
        <div class="top-title">Dashboard</div>

        <div class="top-actions">
            <a href="../index.php" class="textstyle"><span>Buka Situs</span></a>
            <span class="textstyle">Admin</span>
            <a href="../logout.php" class="textstyle"><span >Logout </span></a>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="content">
        <h1>Selamat datang di Dashboard Admin</h1>
    </div>

</div>

<script>
    document.querySelectorAll(".dropdown-li").forEach(item => {
    item.addEventListener("click", function() {
        let submenu = this.querySelector(".submenu");
        submenu.classList.toggle("show");
    });
});

</script>

</body>
</html>
