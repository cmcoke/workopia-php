<?= loadPartial('head'); ?>
<?= loadPartial('navbar'); ?>
<?= loadPartial('top-banner'); ?>


<!-- Job Listings -->
<section>

  <div class="container mx-auto p-4 mt-4">

    <div class="text-center text-3xl mb-4 font-bold border border-gray-300 p-3">

      <?php
      // Check if the 'keywords' variable is set (e.g., when a search has been performed).
      if (isset($keywords)) : ?>
      <!-- Display a heading showing the search results for the given keywords, ensuring special characters are escaped. -->
      Search Results for: <?= htmlspecialchars($keywords) ?>
      <?php else : ?>
      <!-- If no keywords are set, display 'All Jobs' as the heading. -->
      All Jobs
      <?php endif; ?>

    </div>

    <?php
    // Load the 'message' partial, which displays any flash messages (e.g., success or error messages) to the user.
    echo loadPartial('message');
    ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

      <?php
      // Loop through each listing in the $listings array to display them.
      foreach ($listings as $listing) : ?>
      <div class="rounded-lg shadow-md bg-white">
        <div class="p-4">
          <!-- Display the title of the listing. -->
          <h2 class="text-xl font-semibold"><?= $listing->title ?></h2>
          <!-- Display the description of the listing. -->
          <p class="text-gray-700 text-lg mt-2">
            <?= $listing->description ?>
          </p>
          <ul class="my-4 bg-gray-100 p-4 rounded">
            <li class="mb-2">
              <strong>Salary:</strong>
              <!-- Format and display the salary using the formatSalary function. -->
              <?= formatSalary($listing->salary) ?>
            </li>
            <li class="mb-2">
              <!-- Display the location of the listing (city and state). -->
              <strong>Location:</strong> <?= $listing->city ?>, <?= $listing->state ?>
            </li>
            <?php
              // Check if the listing has any tags.
              if (!empty($listing->tags)) : ?>
            <li class="mb-2">
              <!-- Display the tags associated with the listing. -->
              <strong>Tags:</strong> <?= $listing->tags ?>
            </li>
            <?php endif; ?>
          </ul>
          <!-- Provide a link to view more details about the listing. -->
          <a href="/listings/<?= $listing->id ?>"
            class="block w-full text-center px-5 py-2.5 shadow-sm rounded border text-base font-medium text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
            Details
          </a>
        </div>
      </div>
      <?php
      // End the foreach loop.
      endforeach; ?>

    </div>

  </div>

</section>


<?= loadPartial('bottom-banner'); ?>
<?= loadPartial('footer'); ?>