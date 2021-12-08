<div class="app_modal" data-modalclose="true">
    <!--INCOME-->
    <div class="app_modal_box app_modal_income">
        <p class="title icon-calendar-check-o">Nova Receita:</p>
        <form class="app_form" action="" method="post">
            <input type="hidden" value="BRL" name="currency"/>
            <input type="hidden" value="type" name="income"/>

            <label>
                <span class="field icon-leanpub">Descrição:</span>
                <input class="radius" type="text" name="description" placeholder="Ex: Aluguel" required/>
            </label>

            <div class="label_group">
                <label>
                    <span class="field icon-money">Valor:</span>
                    <input class="radius mask-money" type="text" name="description" required/>
                </label>

                <label>
                    <span class="field icon-filter">Data:</span>
                    <input class="radius masc-date" type="date" name="due_at" required/>
                </label>
            </div>

            <div class="label_group">
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
            </div>

            <div class="label_check">
                <p class="field icon-exchange">Repetição:</p>
                <label class="check"
                       data-checkbox="true"
                       data-slideup=".repeate_item_expense, .repeate_item_income">
                    <input type="radio" name="repeat" value="" checked> Única
                </label>

                <label data-checkbox="true"
                       data-slideup=".repeate_item_expense"
                       data-slidedown=".repeate_item_income">
                    <input type="radio" name="repeat" value="income"> Fixa
                </label>

                <label data-checkbox="true"
                       data-slideup=".repeate_item_income"
                       data-slidedown=".repeate_item_expense">
                    <input type="radio" name="repeat" value="expense"> Parcelada
                </label>
            </div>

            <label class="repeate_item repeate_item_income" style="display: none">
                <select name="period">
                    <option value="month">&ofcir; Mensal</option>
                    <option value="year">&ofcir; Anual</option>
                </select>
            </label>

            <label class="repeate_item repeate_item_expense" style="display: none">
                <input class="radius" type="number" min="1" placeholder="1 parcela" name="enrollments"/>
            </label>

            <button class="btn radius transition icon-check-square-o">Lançar Receita</button>
        </form>
    </div>

    <!--EXPENSE-->
    <div class="app_modal_box app_modal_expense">
        <p class="title icon-calendar-minus-o">Nova Despesa:</p>
        <form class="app_form" action="" method="post">
            <input type="hidden" value="BRL" name="currency"/>
            <input type="hidden" value="type" name="expense"/>

            <label>
                <span class="field icon-leanpub">Descrição:</span>
                <input class="radius" type="text" name="description" placeholder="Ex: Aluguel" required/>
            </label>

            <div class="label_group">
                <label>
                    <span class="field icon-money">Valor:</span>
                    <input class="radius mask-money" type="text" name="description" required/>
                </label>

                <label>
                    <span class="field icon-filter">Data:</span>
                    <input class="radius masc-date" type="date" name="due_at" required/>
                </label>
            </div>

            <div class="label_group">
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
            </div>

            <div class="label_check">
                <p class="field icon-exchange">Repetição:</p>
                <label class="check"
                       data-checkbox="true"
                       data-slideup=".repeate_item_expense, .repeate_item_income">
                    <input type="radio" name="repeat" value="" checked> Única
                </label>

                <label data-checkbox="true"
                       data-slideup=".repeate_item_expense"
                       data-slidedown=".repeate_item_income">
                    <input type="radio" name="repeat" value="income"> Fixa
                </label>

                <label data-checkbox="true"
                       data-slideup=".repeate_item_income"
                       data-slidedown=".repeate_item_expense">
                    <input type="radio" name="repeat" value="expense"> Parcelada
                </label>
            </div>

            <label class="repeate_item repeate_item_income" style="display: none">
                <select name="period">
                    <option value="month">&ofcir; Mensal</option>
                    <option value="year">&ofcir; Anual</option>
                </select>
            </label>

            <label class="repeate_item repeate_item_expense" style="display: none">
                <input class="radius" type="number" min="1" placeholder="1 parcela" name="enrollments"/>
            </label>

            <button class="btn radius transition icon-check-square-o">Lançar Despesa</button>
        </form>
    </div>

    <!--SUPPORT-->
    <div class="app_modal_box app_modal_contact">
        <p class="title icon-calendar-minus-o">Fale conosco:</p>
        <form class="app_form" action="" method="post">
            <label>
                <span class="field icon-life-ring">O que precisa?</span>
                <select name="type">
                    <option value="support">&ofcir; Preciso de suporte</option>
                    <option value="suggestion">&ofcir; É uma sugestão</option>
                    <option value="complaint">&ofcir; É uma reclamação</option>
                </select>
            </label>

            <label>
                <span class="field icon-thumb-tack">Assunto:</span>
                <input class="radius" type="text" name="subject"  required/>
            </label>

            <label>
                <span class="field icon-comments-o">Mensagem:</span>
                <textarea class="radius" name="body" rows="4" required></textarea>
            </label>

            <button class="btn radius transition icon-paper-plane-o">Enviar Agora</button>
        </form>
    </div>
</div>