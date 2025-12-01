<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold text-white" href="<?= url('index.php') ?>">Solaz Resort</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navDark">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navDark">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link text-white-50" href="<?= url('index.php') ?>">Home</a></li>
        <li class="nav-item"><a class="nav-link text-white" href="<?= url('rooms.php') ?>">Rooms</a></li>
      </ul>

      <ul class="navbar-nav ms-auto">
        <?php if (!isset($_SESSION['user'])): ?>
          <li class="nav-item"><a class="btn btn-outline-light btn-sm" href="<?= url('login.php') ?>">Login</a></li>
        <?php else: ?>
          <li class="nav-item me-2 text-white-50 small align-self-center">
            Hi, <?= htmlspecialchars($_SESSION['user']['name']) ?>
          </li>
          <li class="nav-item"><a class="btn btn-danger btn-sm" href="<?= url('logout.php') ?>">Logout</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>