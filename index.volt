{{ content() }}


      <br />
      <h2>Book information</h2>
      <br />
        {{ flashSession.output() }}
        <br />
  <div class="container">
     <p>
        <a href="/book/create/" class="btn btn-primary">Add New Book</a>
     </p>
    <table class="table">
      <thead>
        <tr>
          <th>Id</th>
          <th>Book title</th>
          <th>Author title</th>
          <th>Publisher</th>
          <th>Category</th>
          <th>Book Cover</th>
          <th>Edit</th>
          <th>Delete</th>
          <th>Order</th>
        </tr>
      </thead>
      <tbody>
       {% for book in books %}
         <tr>
             <th>{{ book.id }}</th>
             <th>{{ book.bookname }}</th>
             <th>{{ book.authorname }}</th>
             <th>{{ book.publisher }}</th>
             <th>{{ book.category }}</th>
             <th>
                {% set filesCount = book.getFilesbook() | length %}
                {% if filesCount > 0 %}
                <a href="/uploads/{{book.getFilesbook()[0].filename}}">
                    <img src="/uploads/{{book.getFilesbook()[0].filename}}"
                    alt="{{book.getFilesbook()[0].filename}}" id="img" height="70" width="50" border="0" style="display:block;
                    cursor:pointer; border-radius: 50%; " />
                </a>
                {% endif %}
            </th>
             <th><a href="/book/update/{{ book.id }}" class="btn btn-success">Edit</a></th>
             <th><a href="/book/delete/{{ book.id }}" class="btn btn-danger" onclick='return confirm("Are you sure?")'>Delete</a></th>
             <th><a href="/book/order/{{book.id}}"  class="btn btn-warning">Order</a></th>
         </tr>
     {% endfor %}
      </tbody>
    </table>

  </div>


