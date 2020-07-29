# Installation

Run:

    composer require scuti/eloquent-table


Publish the config file

    php artisan vendor:publish --tag=config


### Usage

Insert the trait on model:
    
    use Scuti\EloquentTable\TableTrait;
    
    class Book extends Eloquent {
        // Add this one
        use TableTrait;

    }

Grab records from  model like usual:

    $books = Book::get();

    return view('books.index', compact('books'));

Inside blade view, we just specify the columns we want to show, and then call the render method:

    {!!
        $books->columns(array(
            'id' => 'ID',
            'title' => 'Title',
            'author' => 'Authored By'
        ))
        ->render() 
    !!}

# Handling relationship values using `means($column, $relationship)`:

    {!!
        $books->columns(array(
            'id' => 'ID',
            'title' => 'Title',
            'author' => 'Authored By',
            'owned_by' => 'Owned By',
        ))
        ->means('owned_by', 'user.first_name')
        ->render()
    !!}

# Customizing the display of the column value using `modify($column, $closure)`:

    {!!
        $books->columns(array(
            'id' => 'ID',
            'title' => 'Title',
            'author' => 'Authored By',
            'owned_by' => 'Owned By',
        ))
        ->means('owned_by', 'user')
        ->modify('owned_by', function($user, $book) {
            return $user->first_name . ' ' . $user->last_name;
        })
        ->render() 
    !!}

Using modify, we can specify the column we want to modify, and the function will return the current relationship record (if the column is a relationship),
as well as the current base record, in this case the book.

# Customizing the attributes of each cell of a column using `modifyCell($column, $closure)`:

    {!!
        $books->columns(array(
            'id' => 'ID',
            'title' => 'Title',
            'author' => 'Authored By',
            'owned_by' => 'Owned By',
        ))
        ->means('owned_by', 'user')
        ->modifyCell('owned_by', function($user) {
            return array('class' => $user->role);
        })
        ->render() 
    !!}
Using modifyCell, we can specify the column of the cell we want to modify, and the function should return an array of attributes to be added to the cell.

##### Customizing the attributes of each row in the table using `modifyRow($name, $closure)`:

    {!!
        $books->columns(array(
            'id' => 'ID',
            'title' => 'Title',
            'author' => 'Authored By',
            'owned_by' => 'Owned By',
        ))
        ->means('owned_by', 'user')
        ->modifyRow('mod1', function($user) {
            return array('id' => 'user-'.$user->id);
        })
        ->render() 
    !!}

# With eloquent-table, we can also generate sortable links for columns easily:

In controller:

    $books = Book::sort(Input::get('field'), Input::get('sort'))->get();


In view:

    {!!
        $books->columns(array(
            'id' => 'ID',
            'title' => 'Title',
            'author' => 'Authored By',
            'owned_by' => 'Owned By',
        ))
        ->sortable(array('id', 'title'))
        ->render()
    !!}


# What about if we want to combine this all together, with pagination and sorting?

In your controller:

    $books = Book::sort(Input::get('field'), Input::get('sort'))->paginate(25);
    
    return view('books.index', compact('books'));

In view:

    {!!
        $books->columns(array(
            'id' => 'ID',
            'title' => 'Title',
            'author' => 'Authored By',
            'owned_by' => 'Owned By',
            'publisher' => 'Publisher',
        ))
        ->means('owned_by', 'user')
        ->modify('owned_by', function($user, $book) {
            return $user->first_name . ' ' . $user->last_name;
        })
        ->means('publisher', 'publisher')
        ->modify('publisher', function($publisher, $book) {
            return 'The publisher of this book: '. $publisher->name;
        })
        ->sortable(array('id', 'title'))
        ->showPages()
        ->render()
    !!}

# What if I want to generate a table for a relationship:

In controller:

    $book = Book::with('authors')->find(1);
    
    return view('book.show', compact('book'));

In this case, the book is going to have many authors (`hasMany` relationship)

In view:

    {!!
        $book->authors->columns(
            'id' => 'ID',
            'name' => 'Name',
            'books' => 'Total # of Books'
        )
        ->means('books', 'num_of_books')
        ->render()
    !!}

Keep in mind, we cannot paginate the table, or provide sortable columns on relationships. If you need this, grab it separately:

In controller:

    $book = Book::find(1);

    $authors = Authors::where('book_id', $book->id)->paginate(25);

    return view('books.show', array(
        'book' => $book,
        'authors' => $authors,
    ));

In view:

    {!!
        $authors->columns(array(
            'name' => 'Name',
        ))->render()
    !!}
