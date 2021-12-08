<?php $this->layout("_theme", ["head" => $head]); ?>

    <div class="app_main_box">
        <section class="app_main_left">
            <article class="app_widget">
                <header class="app_widget_title">
                    <h2 class="icon-bar-chart">Controle: (7 dias)</h2>
                </header>
                <div id="control"></div>
            </article>

            <div class="app_main_left_fature">
                <article class="app_widget app_widget_balance">
                    <header class="app_widget_title">
                        <h2 class="icon-calendar-minus-o">Receber:</h2>
                    </header>
                    <div class="app_widget_content">
                        <?php for ($i = 0; $i < 2; $i++): ?>
                            <?= $this->insert("views/balance", ["month" => $i, "status" => "positive"]); ?>
                        <?php endfor; ?>
                        <a href="<?= url("app/receber"); ?>" title="Receitas"
                           class="app_widget_more transition">+ Receitas</a>
                    </div>
                </article>

                <article class="app_widget app_widget_balance">
                    <header class="app_widget_title">
                        <h2 class="icon-calendar-check-o">Pagar:</h2>
                    </header>
                    <div class="app_widget_content">
                        <?php for ($i = 0; $i < 3; $i++): ?>
                            <?= $this->insert("views/balance", ["month" => $i, "status" => "negative"]); ?>
                        <?php endfor; ?>
                        <a href="<?= url("app/pagar"); ?>" title="Despesas"
                           class="app_widget_more transition">+ Despesas</a>
                    </div>
                </article>
            </div>
        </section>

        <section class="app_main_right">
            <ul class="app_widget_shortcuts">
                <li class="income radius transition" data-modalopen=".app_modal_income">
                    <p class="icon-plus-circle">Receita</p>
                </li>
                <li class="expense radius transition" data-modalopen=".app_modal_expense">
                    <p class="icon-plus-circle">Despesa</p>
                </li>
            </ul>

            <article class="app_flex gradient-green">
                <header class="app_flex_title">
                    <h2 class="icon-briefcase">Casa</h2>
                </header>
                <p class="app_flex_amount">R$ 1.285,00</p>
                <p class="app_flex_balance">
                    <span class="income">Receitas: R$ 12,520.00</span>
                    <span class="expense">Despesas: R$ 11,000.00</span>
                </p>
            </article>

            <section class="app_widget app_widget_blog">
                <header class="app_widget_title">
                    <h2 class="icon-graduation-cap">Aprenda:</h2>
                </header>
                <div class="app_widget_content">
                    <?php for ($i = 0; $i < 3; $i++): ?>
                        <article class="app_widget_blog_article">
                            <div class="thumb">
                                <img alt="" title="" src="<?= theme("/assets/images/thumb.jpg", CONF_VIEW_APP); ?>"/>
                            </div>
                            <h3 class="title">
                                <a href="#" title="">Lorem ipsum dolor sit amet, consectetur adipisicing elit.</a>
                            </h3>
                        </article>
                    <?php endfor; ?>
                    <a target="_blank" href="<?= url("/blog"); ?>" title="Blog" class="app_widget_more transition">
                        Ver Mais...</a>
                </div>
            </section>
        </section>
    </div>

<?php $this->start("scripts"); ?>
    <script type="text/javascript">
        Highcharts.setOptions({
            lang: {
                decimalPoint: ',',
                thousandsSep: '.'
            }
        });
        Highcharts.chart('control', {
            chart: {
                type: 'areaspline',
                spacingBottom: 0,
                spacingTop: 5,
                spacingLeft: 0,
                spacingRight: 0,
                height: (9 / 16 * 100) + '%'
            },
            title: null,
            xAxis: {
                categories: [
                    '22/11/2018',
                    '23/11/2018',
                    '24/11/2018',
                    '25/11/2018',
                    '26/11/2018',
                    '27/11/2018',
                    '28/11/2018'
                ],
                minTickInterval: 2
            },
            yAxis: {
                allowDecimals: true,
                title: null,
            },
            tooltip: {
                shared: true,
                valueDecimals: 2,
                valuePrefix: 'R$ '
            },
            credits: {
                enabled: false
            },
            plotOptions: {
                areaspline: {
                    fillOpacity: 0.5
                }
            },
            series: [{
                name: 'Receitas',
                data: [1250, 700, 0, 350, 1000, 580, 300],
                color: '#61DDBC',
                lineColor: '#36BA9B'
            }, {
                name: 'Despesas',
                data: [300, 299, 1250, 1000, 300, 0, 0],
                color: '#F76C82',
                lineColor: '#D94352'
            }]
        });
    </script>
<?php $this->end(); ?>