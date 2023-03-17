<div class="box feed-item">
  <div class="box-body">
    <div class="feed-item-head row mt-20 m-width-20">
      <div class="feed-item-head-photo">
        <a href="<?= $base; ?>/perfil/<?= $data->user->id ?>"><img src="<?= $base; ?>/media/avatars/<?= $data->user->avatar; ?>" /></a>
      </div>
      <div class="feed-item-head-info">
        <a href="<?= $base; ?>/perfil/<?= $data->user->id ?>"><span class="fidi-name"><?= $data->user->name; ?></span></a>
        <span class="fidi-action"><?php
          switch($data->type) {
            case 'text':
              echo 'fez um post';
              break;
            case 'photo':
              echo 'postou uma foto';
              break;
          }
        ?></span>
        <br />
        <span class="fidi-date"><?= date('d/m/Y', strtotime($data->created_at)); ?></span>
      </div>
      <div class="feed-item-head-btn">
        <img src="<?= $base; ?>/assets/images/more.png" />
      </div>
    </div>
    <div class="feed-item-body mt-10 m-width-20"><?php
          switch($data->type) {
            case 'text':
              echo nl2br($data->body);
              break;
            case 'photo':
              echo '<img src="'.$base.'/media/uploads/'.$data->body.'" />';
              break;
          }
        ?>
    </div>
    <div class="feed-item-buttons row mt-20 m-width-20">
      <div class="like-btn <?= ($data->liked ? 'on' : '') ?>"><?= $data->likeCount; ?></div>
      <div class="msg-btn"><?= count($data->comments); ?></div>
    </div>
    <div class="feed-item-comments">
      <?php foreach($data->comments as $comment): ?>
        <div class="fic-item row m-height-10 m-width-20">
          <div class="fic-item-photo">
            <a href=""><img src="media/avatars/default.jpg" /></a>
          </div>
          <div class="fic-item-info">
            <a href="">Bonieky Lacerda</a>
            Muito legal, parabéns!
          </div>
        </div>
      <?php endforeach; ?>
      <div class="fic-answer row m-height-10 m-width-20">
        <div class="fic-item-photo">
          <a href=""><img src="<?= $base; ?>/media/avatars/<?= $loggedUser->avatar; ?>" /></a>
        </div>
        <input type="text" class="fic-item-field" placeholder="Escreva um comentário" />
      </div>
    </div>
  </div>
</div>