# Built in types in ezscript

* object (dynamic and changeable "class")
* array (dynamic array that can store any value)
* float
* uint32
* uint64
* int32
* int64
* int (size is based on process architecture)
* uint (size is based on process architecture)
* string
* byte
* callable
* ptr\<T\> - Reference counted shared pointer
* weakptr\<T\> - Weak pointer to a ptr or ownptr
* ownptr\<T\> - A pointer that is owned by a single reference, only weakptr can be created from one.

Not that these types are **case-insensitive**.
