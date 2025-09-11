<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - PriceComp</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Avatar preview */
        .avatar-preview-container {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        #avatar-preview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 2px solid #ccc;
            object-fit: cover;
            margin-bottom: 0.5rem;
        }

        /* Modal */
        .modal-overlay {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0; top: 0;
            width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.6);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            max-width: 500px;
            width: 90%;
            text-align: center;
        }
        .modal-close {
            float: right;
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* Avatar selection */
        .avatar-selection-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        .avatar-selection-container label {
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .avatar-selection-container img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 2px solid transparent;
            transition: border-color 0.3s;
        }
        .avatar-selection-container input[type="radio"] {
            display: none;
        }
        .avatar-selection-container input[type="radio"]:checked + img {
            border-color: #16a34a; /* green outline when selected */
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-box register-box">
            <h1 class="title">Create Account</h1>
            <h2 class="subtitle">Join PriceComp Today!</h2>

            <?php
                if (isset($_GET['error'])) {
                    echo '<p class="error-message">' . htmlspecialchars($_GET['error']) . '</p>';
                }
            ?>

            <form action="register_action.php" method="POST">
                <!-- Avatar Preview + Select -->
                <div class="avatar-preview-container">
                    <img src="https://placehold.co/100x100/EFEFEF/AAAAAA?text=Avatar" 
                         id="avatar-preview" alt="Selected Avatar">
                    <input type="hidden" name="profile_photo" id="profile_photo_input" required>
                    <br>
                    <button type="button" class="btn-secondary" onclick="openModal()">Choose Avatar</button>
                </div>

                <div class="input-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="input-group">
                    <label>Gender</label>
                    <div class="gender-options">
                        <input type="radio" id="male" name="gender" value="Male" required>
                        <label for="male">Male</label>
                        <input type="radio" id="female" name="gender" value="Female">
                        <label for="female">Female</label>
                    </div>
                </div>
                <div class="input-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="input-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn">Register</button>
            </form>
            <div class="links">
                <p>Already have an account? <a href="login.php">Login</a></p>
            </div>
        </div>
    </div>

    <!-- Avatar Selection Modal -->
    <div id="avatar-modal" class="modal-overlay">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal()">&times;</span>
            <h3>Choose Your Avatar</h3>
            <div class="avatar-selection-container">
                <label>
                    <input type="radio" name="avatar_choice" value="avatar1.gif" onchange="selectAvatar('avatar1.gif')">
                    <img src="avatars/avatar1.gif" alt="Avatar 1">
                </label>
                <label>
                    <input type="radio" name="avatar_choice" value="avatar2.gif" onchange="selectAvatar('avatar2.gif')">
                    <img src="avatars/avatar2.gif" alt="Avatar 2">
                </label>
                <label>
                    <input type="radio" name="avatar_choice" value="avatar3.gif" onchange="selectAvatar('avatar3.gif')">
                    <img src="avatars/avatar3.gif" alt="Avatar 3">
                </label>
                <label>
                    <input type="radio" name="avatar_choice" value="avatar4.gif" onchange="selectAvatar('avatar4.gif')">
                    <img src="avatars/avatar4.gif" alt="Avatar 4">
                </label>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById('avatar-modal');
        const previewImg = document.getElementById('avatar-preview');
        const photoInput = document.getElementById('profile_photo_input');

        function openModal() {
            modal.style.display = 'flex';
        }

        function closeModal() {
            modal.style.display = 'none';
        }

        function selectAvatar(filename) {
            // Update preview + hidden input
            previewImg.src = 'avatars/' + filename;
            photoInput.value = filename;
            closeModal();
        }

        // Close modal if user clicks outside
        window.onclick = function(event) {
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>
