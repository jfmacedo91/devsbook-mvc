<?= $render('header', ['loggedUser' => $loggedUser]); ?>
<section class="container main">
  <?php if($user->id == $loggedUser->id) {
    echo $render('sidebar', ['activeMenu'=>'profile']); 
  } else {
    echo $render('sidebar'); 
  } ?>
  <section class="feed">

    <div class="row">
      <div class="box flex-1 border-top-flat">
        <div class="box-body">
          <div class="profile-cover" style="background-image: url('<?= $base; ?>/media/covers/<?= $user->cover; ?>');"></div>
          <div class="profile-info m-20 row">
            <div class="profile-info-avatar">
              <img src="<?= $base; ?>/media/avatars/<?= $user->avatar; ?>" />
            </div>
            <div class="profile-info-name">
              <div class="profile-info-name-text"><?= $user->name; ?></div>
              <div class="profile-info-location"><?= $user->city; ?></div>
            </div>
            <div class="profile-info-data row">
              <div class="profile-info-item m-width-20">
                <div class="profile-info-item-n"><?= count($user->followers); ?></div>
                <div class="profile-info-item-s">Seguidores</div>
              </div>
              <div class="profile-info-item m-width-20">
                <div class="profile-info-item-n"><?= count($user->following); ?></div>
                <div class="profile-info-item-s">Seguindo</div>
              </div>
              <div class="profile-info-item m-width-20">
                <div class="profile-info-item-n"><?= count($user->photos); ?></div>
                <div class="profile-info-item-s">Fotos</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

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
              <a href="">ver todos</a>
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

        <div class="box">
          <div class="box-header m-10">
            <div class="box-header-text">
              Fotos
              <span>(<?= count($user->photos); ?>)</span>
            </div>
            <div class="box-header-buttons">
              <a href="">ver todos</a>
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
</section>
<?= $render('footer'); ?>