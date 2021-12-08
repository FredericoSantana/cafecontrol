<div class="balance <?= $status; ?>">
    <p class="desc">
        <b class="app_invoice_link transition"><a href="<?= url("app/fatura/22"); ?>">Salário</a></b>
        <span class="date"><?= date("d/m/Y", strtotime("+{$month}month")); ?></span>
    </p>
    <p class="price">
        R$ 2.200,00
        <span title="Receber" class="check icon-thumbs-o-down transition"
              data-toggleclass="active icon-thumbs-o-down icon-thumbs-o-up"></span>
    </p>
</div>