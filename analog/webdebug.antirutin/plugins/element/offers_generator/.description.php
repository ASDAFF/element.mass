<p>Плагин выполняет простую генерацию торговых предложений (ТП), доступно два режима.</p>
<h3>Режим: по умолчанию (по свойствам)</h3>
<p>Модуль генерирует ТП как все доступные комбинации значений выбранных свойств. <span style="color:red'"><b>Поэтому, будьте аккуратны!</b> Нечаянно можно сгенерировать миллионы ненужных ТП</b>.
<p>Перед создание ТП модуль проверяет его наличие: если такое ТП уже есть, новое не будет создано.</p>
<p>Символьный код ТП генерируется как символьный код родительского товара + коды значений свойств (транслит), разделённые символами подчёркивания.</p>
<p>Для каждого ТП модуль копирует название родительского товара, и заполняет значения выбранных свойств. Кроме этого, из родительского товара копируются все цены.</p>
<h3>Режим: простой</h3>
<p>Модуль просто создаёт заданное количество ТП, без заполнения значений свойств. Подходит для случаев, когда данные будут в дальнейшем заполняться дополнительно.</p>
<p>Торговые предложения создаются каждый раз, без дополнительных проверок. Т.е. если запустить два раза процесс генерации 10 ТП, то у товара добавится 20 ТП.</p>
<br/>