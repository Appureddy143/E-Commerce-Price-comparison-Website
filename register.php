<?php
session_start();

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    // If logged in, redirect them to their panel, not the register page
    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
        header("Location: admin_panel.php");
    } else {
        header("Location: user_panel.php");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
<!-- ... existing code ... -->
    <style>
        /* Avatar preview */
        .avatar-preview-container {
/* ... existing code ... */
        .avatar-selection-container input[type="radio"]:checked + img {
            border-color: #16a34a; /* green outline when selected */
        }
    </style>
</head>
<body>
    <div class="auth-container">
<!-- ... existing code ... -->
            <form action="register_action.php" method="POST">
                <!-- Avatar Preview + Select -->
                <div class="avatar-preview-container">
                    <img src="https://placehold.co/100x100/EFEFEF/AAAAAA?text=Avatar" 
                         id="avatar-preview" alt="Selected Avatar">
                    <!-- Set a default value in case user doesn't choose one -->
                    <input type="hidden" name="profile_photo" id="profile_photo_input" value="avatar1.gif" required> 
                    <br>
                    <button type="button" class="btn-secondary" onclick="openModal()">Choose Avatar</button>
                </div>

                <div class="input-group">
<!-- ... existing code ... -->
    <div id="avatar-modal" class="modal-overlay">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal()">&times;</span>
            <h3>Choose Your Avatar</h3>
            <div class="avatar-selection-container">
                <label>
                    <input type="radio" name="avatar_choice" value="avatar1.gif" onchange="selectAvatar('avatar1.gif')">
                    <!-- Add onerror fallback images -->
                    <img src="avatars/avatar1.gif" alt="Avatar 1" onerror="this.src='https://placehold.co/80x80?text=1'">
                </label>
                <label>
                    <input type="radio" name="avatar_choice" value="avatar2.gif" onchange="selectAvatar('avatar2.gif')">
                    <img src="avatars/avatar2.gif" alt="Avatar 2" onerror="this.src='https://placehold.co/80x80?text=2'">
                </label>
                <label>
                    <input type="radio" name="avatar_choice" value="avatar3.gif" onchange="selectAvatar('avatar3.gif')">
                    <img src="avatars/avatar3.gif" alt="Avatar 3" onerror="this.src='https://placehold.co/80x80?text=3'">
                </label>
                <label>
                    <input type="radio" name="avatar_choice" value="avatar4.gif" onchange="selectAvatar('avatar4.gif')">
                    <img src="avatars/avatar4.gif" alt="Avatar 4" onerror="this.src='https://placehold.co/80x80?text=4'">
                </label>
            </div>
        </div>
    </div>

    <script>
<!-- ... existing code ... -->
        const photoInput = document.getElementById('profile_photo_input');

        function openModal() {
/* ... existing code ... */
        function selectAvatar(filename) {
            // Update preview + hidden input
            previewImg.src = 'avatars/' + filename;
            // Add fallback for preview image as well
            previewImg.onerror = () => { previewImg.src = 'https://placehold.co/100x100?text=Avatar'; }; 
            photoInput.value = filename;
            closeModal();
        }

        // Close modal if user clicks outside
/* ... existing code ... */
    </script>
</body>
</html>
