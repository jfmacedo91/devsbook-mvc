<?= $render('header', ['loggedUser' => $loggedUser]); ?>
<section class="container main">
  <?= $render('sidebar'); ?>

  <section class="feed">
    <div class="box feed-new m-10">
      <div class="box-body m-10">
        Você pesquisou por: <strong><?= $searchTerm ?></strong>
      </div>
    </div>

    <div class="box feed-new m-10">
      <div class="box-body">
        <div class="full-friend-list">
          <?php foreach($users as $user): ?>
            <div class="friend-icon">
              <a href="<?= $base; ?>/perfil/<?= $user->id ?>">
                <div class="friend-icon-avatar">
                  <img src="<?= $base; ?>/media/avatars/<?= $user->avatar ?>" />
                </div>
                <div class="friend-icon-name">
                  <?= $user->name ?>
                </div>
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>
</section>
<?= $render('footer'); ?>