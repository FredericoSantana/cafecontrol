<?php $this->layout("_theme", ["head" => $head]); ?>

<div class="app_invoice app_widget">
    <form class="app_form" action="" method="post">
        <input type="hidden" value="BRL" name="currency"/>
        <input type="hidden" value="type" name="income"/>

        <label>
            <span class="field icon-leanpub">Descrição:</span>
            <input class="radius" type="text" name="description" placeholder="Ex: Aluguel" required/>
        </label>

        <label>
            <span class="field icon-money">Valor:</span>
            <input class="radius mask-money" type="text" name="description" required/>
        </label>

        <label>
            <span class="field icon-filter">Data:</span>
            <input class="radius masc-date" type="date" name="due_at" required/>
        </label>

        <label>
            <span class="field icon-briefcase">Carteira:</span>
            <select name="wallet">
                <option value="1">&ofcir; Casa</option>
            </select>
        </label>

        <label>
            <span class="field icon-filter">Categoria:</span>
            <select name="category">
                <option value="1">&ofcir; Salário</option>
            </select>
        </label>

        <div class="al-center">
            <div class="label_check">
                <label data-checkbox="true"
                       data-slideup=".repeate_item_expense"
                       data-slidedown=".repeate_item_income">
                    <input type="checkbox" name="repeat" value="income"> Atualizar parcelas futuras
                </label>
            </div>

            <div>
                <span class="btn_remove transition icon-error">Excluir</span>
                <button class="btn btn_inline radius transition icon-pencil-square-o">Atualizar</button>
            </div>
        </div>
    </form>
</div>