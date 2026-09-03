    </main>

    <!-- Footer -->
    <footer class="bg-dark text-light mt-5 py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <h5><i class="fas fa-utensils"></i> <?php echo $siteName; ?></h5>
                    <p><?php echo $siteDescription; ?></p>
                </div>
                <div class="col-md-3 mb-3">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <?php if (strpos($_SERVER['PHP_SELF'], '/admin/') === false): ?>
                            <li><a href="index.php" class="text-light text-decoration-none">Home</a></li>
                            <li><a href="menu.php" class="text-light text-decoration-none">Menu</a></li>
                            <li><a href="about.php" class="text-light text-decoration-none">About Us</a></li>
                            <li><a href="contact.php" class="text-light text-decoration-none">Contact</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="col-md-3 mb-3">
                    <h5>Contact Info</h5>
                    <p>
                        <i class="fas fa-phone"></i> <?php echo getSetting('phone') ?? '+977-9800000000'; ?><br>
                        <i class="fas fa-envelope"></i> <?php echo getSetting('email') ?? 'info@royalfoodsewa.com'; ?><br>
                        <i class="fas fa-map-marker-alt"></i> <?php echo getSetting('address') ?? 'Kathmandu, Nepal'; ?>
                    </p>
                </div>
                <div class="col-md-3 mb-3">
                    <h5>Business Hours</h5>
                    <p>
                        <strong>Opening Time:</strong> <?php echo getSetting('opening_time') ?? '09:00 AM'; ?><br>
                        <strong>Closing Time:</strong> <?php echo getSetting('closing_time') ?? '10:00 PM'; ?>
                    </p>
                </div>
            </div>
            <hr class="bg-light">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; <?php echo date('Y'); ?> <?php echo $siteName; ?>. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-end">
                    <p>Powered by <strong>Royal Food Sewa</strong></p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? '../assets/js/script.js' : 'assets/js/script.js'; ?>"></script>
    
    <!-- Flash Messages -->
    <?php
    if ($flash = getFlash('success')):
    ?>
        <script>
            $(document).ready(function() {
                showAlert('success', '<?php echo addslashes($flash); ?>');
            });
        </script>
    <?php endif; ?>
    
    <?php
    if ($flash = getFlash('error')):
    ?>
        <script>
            $(document).ready(function() {
                showAlert('error', '<?php echo addslashes($flash); ?>');
            });
        </script>
    <?php endif; ?>
</body>
</html>
