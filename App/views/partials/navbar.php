<?php
  use Framework\Session; // Import the Session class from the Framework namespace
?>

<header class="bg-blue-900 text-white p-4">
  <div class="container mx-auto flex justify-between items-center">
    <h1 class="text-3xl font-semibold">
      <a href="/">Workopia</a>
    </h1>
    <nav class="space-x-4">

      <?php 
        // Check if a user session exists (i.e., the user is logged in)
        if (Session::has('user')) : 
      ?>
      <div class="flex justify-between items-center gap-4">

        <!-- Display a welcome message with the user's name -->
        <div class="text-blue-500">
          Welcome <?= Session::get('user')['name'] ?>
        </div>

        <!-- Logout form that sends a POST request to the /auth/logout endpoint -->
        <form method="POST" action="/auth/logout">
          <button type="submit" class="text-white inline hover:underline">Logout</button>
        </form>

        <!-- Button for posting a job, visible only to logged-in users -->
        <a href="/listings/create"
          class="bg-yellow-500 hover:bg-yellow-600 text-black px-4 py-2 rounded hover:shadow-md transition duration-300">
          <i class="fa fa-edit"></i> Post a Job
        </a>
      </div>

      <?php else : ?>
      <!-- If the user is not logged in, show login and register links -->
      <a href="/auth/login" class="text-white hover:underline">Login</a>
      <a href="/auth/register" class="text-white hover:underline">Register</a>
      <?php endif; ?>

    </nav>
  </div>
</header>