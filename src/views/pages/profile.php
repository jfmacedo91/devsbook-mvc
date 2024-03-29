<?= $render('header', ['loggedUser' => $loggedUser]); ?>
<section class="container main">
  <?php if($user->id == $loggedUser->id) {
    echo $render('sidebar', ['activeMenu'=>'profile']); 
  } else {
    echo $render('sidebar'); 
  } ?>
  <section class="feed">
    <?= $render('perfil-header', ['user'=>$user, 'loggedUser'=>$loggedUser, 'isFollowing'=>$isFollowing]) ?>

    <div class="row">
      <div class="column side pr-5">
        <div class="box">
          <div class="box-body">
            <div class="user-info-mini">
              <img src="<?= $base; ?>/assets/images/calendar.png" />
              <?= date('d/m/Y', strtotime($user->birthdate)); ?> (<?= $user->age; ?> anos)
            </div>

            <?php if(!empty($user->city)): ?>
              <div class="user-info-mini">
                <img src="<?= $base; ?>/assets/images/pin.png" />
                <?= $user->city; ?>
              </div>
            <?php endif; ?>

            <?php if(!empty($user->work)): ?>
              <div class="user-info-mini">
                <img src="<?= $base; ?>/assets/images/work.png" />
                <?= $user->work; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <div class="box">
          <div class="box-header m-10">
            <div class="box-header-text">
              Seguindo
              <span>(<?= count($user->following); ?>)</span>
            </div>
            <div class="box-header-buttons">
              <a href="<?= $base; ?>/perfil/<?= $user->id ?>/amigos?tab=following">ver todos</a>
            </div>
          </div>
          <div class="box-body friend-list">
            <?php for ($index=0; $index < 9; $index++): ?>
              <?php if(isset($user->following[$index])): ?>
                <div class="friend-icon">
                  <a href="<?= $base; ?>/perfil/<?= $user->following[$index]->id ?>">
                    <div class="friend-icon-avatar">
                      <img src="<?= $base; ?>/media/avatars/<?= $user->following[$index]->avatar; ?>" />
                    </div>
                    <div class="friend-icon-name">
                      <?= $user->following[$index]->name; ?>
                    </div>
                  </a>
                </div>
              <?php endif; ?>
            <?php endfor; ?>
          </div>
        </div>
      </div>

      <div class="column pl-5">
        <?php if(count($user->photos) != 0): ?>
          <div class="box">
            <div class="box-header m-10">
              <div class="box-header-text">
                Fotos
                <span>(<?= count($user->photos); ?>)</span>
              </div>
              <div class="box-header-buttons">
                <a href="<?= $base; ?>/perfil/<?= $user->id; ?>/fotos">ver todos</a>
              </div>
            </div>

            <div class="box-body row m-20">
              <?php for ($index=0; $index < 4; $index++): ?>
                <?php if(isset($user->photos[$index])): ?>
                  <div class="user-photo-item">
                    <a href="#modal-<?= $user->photos[$index]->id; ?>" rel="modal:open">
                      <img src="<?= $base; ?>/media/uploads/<?= $user->photos[$index]->body; ?>" />
                    </a>
                    <div id="modal-<?= $user->photos[$index]->id; ?>" style="display:none">
                      <img src="<?= $base; ?>/media/uploads/<?= $user->photos[$index]->body; ?>" />
                    </div>
                  </div>
                <?php endif; ?>
              <?php endfor; ?>
            </div>
          </div>
        <?php endif; ?>

        <?php if($user->id == $loggedUser->id) {
          echo $render('feed-editor', ['user'=>$loggedUser]);
        } ?>

        <?php foreach($feed['posts'] as $feedItem): ?>
          <?= $render('feed-item', [
            'loggedUser'=>$loggedUser,
            'data'=>$feedItem
          ]); ?>
        <?php endforeach ?>

        <div class="feed-pagination">
          <?php for($index = 0; $index < $feed['pagesCount']; $index++): ?>
            <a class="<?= ($index == $feed['currentPage'] ? 'active' : '') ?>" href="<?= $base; ?>/perfil/<?= $user->id; ?>?page=<?= $index; ?>"><?= $index + 1 ?></a>
          <?php endfor; ?>
        </div>
      </div>
    </div>
  </section>
</section>
<?= $render('footer'); ?>