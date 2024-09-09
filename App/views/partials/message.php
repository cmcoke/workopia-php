<?php use Framework\Session; // Import the Session class from the Framework namespace to manage session data. ?>

<?php $successMessage = Session::getFlashMessage('success_message'); // Retrieve any success message stored in the session flash messages. ?>

<?php if ($successMessage !== null) : // Check if a success message exists. ?>
<div class="message bg-green-100 p-3 my-3">
  <?= $successMessage // Display the success message in a styled div. ?>
</div>
<?php endif; ?>

<?php $errorMessage = Session::getFlashMessage('error_message'); // Retrieve any error message stored in the session flash messages. ?>

<?php if ($errorMessage !== null) : // Check if an error message exists. ?>
<div class="message bg-red-100 p-3 my-3">
  <?= $errorMessage // Display the error message in a styled div. ?>
</div>
<?php endif; ?>