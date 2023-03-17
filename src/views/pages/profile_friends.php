<?= $render('header', ['loggedUser' => $loggedUser]); ?>
<section class="container main">
  <?php if($user->id == $loggedUser->id) {
    echo $render('sidebar', ['activeMenu'=>'friends']); 
  } else {
    echo $render('sidebar'); 
  } ?>
  <section class="feed">
    <?= $render('perfil-header', ['user'=>$user, 'loggedUser'=>$loggedUser, 'isFollowing'=>$isFollowing]) ?>

    <div class="row">
      <div class="column">
        <div class="box">
          <div class="box-body">
            <div class="tabs">
              <div class="tab-item <?= ($tab == 'followers') ? 'active': '' ?>" data-for="followers">
                Seguidores
              </div>
              <div class="tab-item <?= ($tab == 'following') ? 'active': '' ?>" data-for="following">
                Seguindo
              </div>
            </div>
            <div class="tab-content">
              <div class="tab-body" data-item="followers">
                <div class="full-friend-list">
                  <?php foreach($user->followers as $follower): ?>
                    <div class="friend-icon">
                      <a href="<?= $base; ?>/perfil/<?= $follower->id ?>">
                        <div class="friend-icon-avatar">
                          <img src="<?= $base; ?>/media/avatars/<?= $follower->avatar ?>" />
                        </div>
                        <div class="friend-icon-name">
                          <?= $follower->name ?>
                        </div>
                      </a>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>

              <div class="tab-body" data-item="following">
                <div class="full-friend-list">
                  <?php foreach($user->following as $follow): ?>
                    <div class="friend-icon">
                      <a href="<?= $base; ?>/perfil/<?= $follow->id ?>">
                        <div class="friend-icon-avatar">
                          <img src="<?= $base; ?>/media/avatars/<?= $follow->avatar ?>" />
                        </div>
                        <div class="friend-icon-name">
                          <?= $follow->name ?>
                        </div>
                      </a>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</section>
<?= $render('footer'); ?>