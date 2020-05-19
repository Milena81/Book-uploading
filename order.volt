{{ content() }}

    <br />
    <h2>Съдържание на кошницата</h2>
    <br />

<div class="container">
<p>
    <a href="/book/index/" class="btn btn-primary">Back</a>
<table class="table">
  <thead>
    <tr>
        <th style="text-align: left;"> Продукт: </th>
        <th style="text-align: right; width: 75px;"> Ед. цена: </th>
        <th style="text-align: right; width: 75px;> Количество: </th>
        <th style="text-align: right; width: 75px;> Общо: </th>
    </tr>
  </thead>
  <tbody>
    <tr>
        <td style="width: 40px; vertical-align: middle; text-align: center;">
            {{book.bookname}}
        </td>
        <td style="width: 40px; vertical-align: middle; text-align: center;">
            {{book.authorname}}
        </td>
        <td style="width: 40px; vertical-align: middle; text-align: center;">
            лв.
        </td>
        <td style="width: 40px; vertical-align: middle; text-align: center;">
            лв.
        </td>
    </tr>
  </tbody>
</table>
    <p>Цена за доставка:</p>
</div>