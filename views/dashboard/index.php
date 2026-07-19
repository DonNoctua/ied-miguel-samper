<?php
/**
 * VISTA: DASHBOARD
 * IED Miguel Samper Agudelo
 */

if (!isLoggedIn()) redirect(BASE_URL . 'login');

$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - IED Miguel Samper</title>
    <link rel="stylesheet" href="<?= PUBLIC_URL ?>css/style.css">
    <link rel="stylesheet" href="<?= PUBLIC_URL ?>css/dashboard.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <div class="navbar-brand">IED Miguel Samper</div>
            <ul class="navbar-nav">
                <li><a href="<?= BASE_URL ?>dashboard">Dashboard</a></li>
                <li><a href="<?= BASE_URL ?>perfil">Perfil</a></li>
                <li><a href="<?= BASE_URL ?>logout">Cerrar Sesión</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-brand">Menú</div>
            <ul class="sidebar-menu">
                <li><a href="<?= BASE_URL ?>dashboard" class="active">Dashboard</a></li>
                
                <?php if (hasPermission(1)): ?>
                    <!-- Admin -->
                    <li><a href="<?= BASE_URL ?>admin/usuarios">Usuarios</a></li>
                    <li><a href="<?= BASE_URL ?>estudiantes">Estudiantes</a></li>
                    <li><a href="<?= BASE_URL ?>matriculas">Matrículas</a></li>
                    <li><a href="<?= BASE_URL ?>admin/noticias">Noticias</a></li>
                    <li><a href="<?= BASE_URL ?>admin/galeria">Galería</a></li>
                    <li><a href="<?= BASE_URL ?>admin/contactos">Contactos</a></li>
                <?php elseif (hasPermission(2)): ?>
                    <!-- Rector -->
                    <li><a href="<?= BASE_URL ?>matriculas">Matrículas</a></li>
                    <li><a href="<?= BASE_URL ?>estudiantes">Estudiantes</a></li>
                <?php elseif (hasPermission(3)): ?>
                    <!-- Coordinador -->
                    <li><a href="<?= BASE_URL ?>admin/noticias">Noticias</a></li>
                    <li><a href="<?= BASE_URL ?>estudiantes">Estudiantes</a></li>
                    <li><a href="<?= BASE_URL ?>admin/galeria">Galería</a></li>
                <?php elseif (hasPermission(4)): ?>
                    <!-- Secretaría -->
                    <li><a href="<?= BASE_URL ?>matriculas">Matrículas</a></li>
                    <li><a href="<?= BASE_URL ?>estudiantes">Estudiantes</a></li>
                <?php elseif (hasPermission(5)): ?>
                    <!-- Docente -->
                    <li><a href="<?= BASE_URL ?>estudiantes">Estudiantes</a></li>
                    <li><a href="<?= BASE_URL ?>noticias">Noticias</a></li>
                <?php endif; ?>
            </ul>
        </aside>

        <!-- Contenido Principal -->
        <main class="main-content">
            <div class="page-header">
                <div class="page-title">
                    <h1>Bienvenido, <?= $user['nombre'] ?></h1>
                    <p>Rol: <?= $user['rol'] ?></p>
                </div>
            </div>

            <?php if ($success = getFlash('success')): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <!-- Estadísticas -->
            <div class="stats-grid">
                <?php if (hasPermission(1)): ?>
                    <div class="stat-card">
                        <div class="stat-value"><?= $usuarios_count ?? 0 ?></div>
                        <div class="stat-label">Usuarios</div>
                    </div>
                    <div class="stat-card secondary">
                        <div class="stat-value"><?= $estudiantes_count ?? 0 ?></div>
                        <div class="stat-label">Estudiantes</div>
                    </div>
                    <div class="stat-card info">
                        <div class="stat-value"><?= $matriculas_count ?? 0 ?></div>
                        <div class="stat-label">Matrículas</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?= $noticias_count ?? 0 ?></div>
                        <div class="stat-label">Noticias</div>
                    </div>
                <?php elseif (hasPermission(2)): ?>
                    <div class="stat-card">
                        <div class="stat-value"><?= $estudiantes_count ?? 0 ?></div>
                        <div class="stat-label">Estudiantes</div>
                    </div>
                    <div class="stat-card secondary">
                        <div class="stat-value"><?= $matriculas_count ?? 0 ?></div>
                        <div class="stat-label">Matrículas</div>
                    </div>
                <?php elseif (hasPermission(3)): ?>
                    <div class="stat-card">
                        <div class="stat-value"><?= $noticias_count ?? 0 ?></div>
                        <div class="stat-label">Noticias</div>
                    </div>
                    <div class="stat-card secondary">
                        <div class="stat-value"><?= $galeria_count ?? 0 ?></div>
                        <div class="stat-label">Imágenes</div>
                    </div>
                    <div class="stat-card info">
                        <div class="stat-value"><?= $estudiantes_count ?? 0 ?></div>
                        <div class="stat-label">Estudiantes</div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Últimas Matrículas -->
            <?php if (isset($matriculas_recientes) && count($matriculas_recientes) > 0): ?>
                <div class="card">
                    <div class="card-header">Matrículas Recientes</div>
                    <div class="card-body">
                        <table>
                            <thead>
                                <tr>
                                    <th>Estudiante</th>
                                    <th>Grado</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($matriculas_recientes as $m): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($m['nombre'] . ' ' . $m['apellido']) ?></td>
                                        <td><?= htmlspecialchars($m['grado']) ?></td>
                                        <td>
                                            <span class="badge" style="background: <?= $m['estado'] === 'aprobada' ? '#28a745' : ($m['estado'] === 'rechazada' ? '#dc3545' : '#ffc107') ?>; color: white; padding: 0.25rem 0.5rem; border-radius: 4px;">
                                                <?= ucfirst($m['estado']) ?>
                                            </span>
                                        </td>
                                        <td><?= formatDate($m['created_at']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
