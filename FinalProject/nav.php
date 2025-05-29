<nav>
    <ul>
        <li><a href="index.php">Avaleht</a></li>
        <li><a href="broneeringud.php">Broneeri laud</a></li>
        <li><a href="Menuu.php">Menuu</a></li>
        <?php if (isset($_SESSION['kasutaja'])): ?>
            <li><a href="adminPanel.php">Broneeringute haldus</a></li>
            <li>
                <form action="logout.php" method="post" style="display:inline;">
                    <button type="submit" name="logout">Logi välja (<?=htmlspecialchars($_SESSION['kasutaja'])?>)</button>
                </form>
            </li>
        <?php else: ?>
            <li><a href="login2.php">Logi sisse</a></li>
        <?php endif; ?>
    </ul>
</nav>