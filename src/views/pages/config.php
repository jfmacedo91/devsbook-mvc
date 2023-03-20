<?= $render('header', ['loggedUser' => $loggedUser]); ?>
<section class="container main">
  <?= $render('sidebar', ['activeMenu'=>'config']); ?>
  <section class="feed mt-10">
    <div class="box">
      <div class="column m-10 pr-10 pl-10">
        <h1>Configurações</h1>
        <form class="config column" enctype="multipart/form-data" action="<?= $base; ?>/config" method="POST">
          <?php if(!empty($flash)): ?>
            <span class="flash mt-10"><?= $flash ?></span>
          <?php endif; ?>
          <label for="avatar">Novo avatar:</label>
          <input type="file" name="avatar" id="avatar">
          <label for="cover">Nova capa:</label>
          <input type="file" name="cover" id="cover">
          <hr class="mt-10">
          <label class="mt-10" for="name">Nome completo:</label>
          <input type="text" name="name" id="name" value="<?= $user->name; ?>">
          <label class="mt-10" for="birthdate">Data de nascimentto:</label>
          <input type="text" name="birthdate" id="birthdate" value="<?= date('d/m/Y', strtotime($user->birthdate)); ?>">
          <label class="mt-10" for="email">E-mail:</label>
          <input type="email" name="email" id="email" value="<?= $user->email; ?>">
          <label class="mt-10" for="city">Cidade:</label>
          <input type="text" name="city" id="city" value="<?= $user->city; ?>">
          <label class="mt-10" for="work">Trabalho:</label>
          <input type="text" name="work" id="work" value="<?= $user->work; ?>">
          <hr class="mt-10">
          <label class="mt-10" for="password">Nova senha:</label>
          <input type="password" name="password" id="password">
          <label class="mt-10" for="passwordConfirm">Confirmar nova senha:</label>
          <input type="password" name="passwordConfirm" id="passwordConfirm">
          <button type="submit" class="button">Atualizar</button>
        </form>
      </div>
    </div>
  </section>
</section>
<?= $render('footer'); ?>