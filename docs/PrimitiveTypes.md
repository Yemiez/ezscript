# Built in types in ezscript

* object (dynamic and changeable "class")
* array (dynamic array that can store any value)
* float
* int
* string
* callable
* ptr\<T\> - Reference counted shared pointer
* weakptr\<T\> - Weak pointer to a ptr or ownptr
* ownptr\<T\> - A pointer that is owned by a single reference, only weakptr can be created from one.

Not that these types are **case-insensitive**.
