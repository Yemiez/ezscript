# Syntax (and Semantic)

## General rules

* Statements are terminated with a `;` character.
* Variables are immutable by default.
* A pointer can only be created from a mutable variable.

## Variables

#### Immutable variable declaration

```ezscript
var variable: type = ...;
// Examples:
var firstName: string = "John";
var lastName: string = "Doe";
var age: int = 23;

// These are immutable, so trying to modify them would cause an error
age++; // Throws a type error
```

### Mutable variable declarations

```ezscript
var variable: mut type = ...;
// Examples
var firstName: mut string = "John";
var lastName: mut string = "Doe";
var age: mut int = 23;

age++; // This is okay
```

### Pointer variable declarations

```ezscript
var variable: type = ...;
var pointerVariable: ptr<type> = &variable;

// examples
var firstName: string = "John";
var lastName: string = "Doe";
var age: mut int = 23;

// Creating a shared pointer to age
var agePtr: ptr<mut int> = &age;

// Creating a shared ptr to a new int
var newPtr: ptr<mut int> = new int(99);
++*newPtr;

*agePtr; // Indirection (dereference the pointer) returns the value 25.
++*agePtr; // `age` is now 26.
```

Using different pointer types like `ptr`, `ownptr`, and `weakptr`.

```ezscript
var myWeakPtr: weakptr<mut int> = null;
// doing *myWeakPtr or anything to retrieve/modify will throw NullException

{ // Enter a new scope for scope auto-destruction
    var ownPtr: ownptr<mut int> = 1337;
    
    // Assignment is simple (and works with both ptr and ownptr)
    myWeakPtr = ownPtr;
    ++myWeakPtr; // ownPtr value is not 1338
}

if (myWeakPtr.expired()) {
    // The weak ptr has expired because the lifetime of ownPtr ended.
    // e.g. the following will throw a NullException
    ++myWeakPtr;
}
```

## Functions

## General rules

* Primitive immutable variables are passed to function by value.
* Primitive mutable variables are passed to function by pointer/reference.
* Functions can be templated

## Functions outside of classes

Global (in current module) function:

```ezscript
fun add(a: int, b: int): int {
    ret a + b;
}
```

Lambda functions:

```ezscript
var events: object = {}

fn listen(eventName: string, callback: callable): void {
    if (events.has(eventName)) {
        events[eventName] = []
    } 
}

fn trigger(eventName: string, eventData: array): void {
    if (!events.has(eventName)) {
        ret;
    }
    
    events[eventName].forEach(fn (callback): void => {
        callback(eventData); 
    });
}

listen('click', fn (data: array) {
    // Just an example...
    __context.logger.debug('event data', data);
});
```

Templated function:

```ezscript
template<T>
fn add(a: T, b: T): T => a + b;

add(5, 5); // Returns 10
add('Hello ', 'world'); // return 'Hello world'
```

## Classes

### Concepts

* Abstract classes cannot be instantiated ()
* Classes can extend multiple classes and implement multiple interfaces.
* Classes are private by default and must be explicitly exported from a module by using an export statement or marking
  it as a `pub` class.
* Classes can contain public, private, and protected methods.
* All members of classes are private, no exceptions.
* Classes can have basic templated types, i.e. like ptr.
* All methods are public by default.
* Both `fn` and eventual other attributes of a function can be emitted.

### Very simple Person class

```ezscript
class Person {
    var name: mut string;
    var age: mut int;
    
    // Automatically generate appropiate constructors (same as below)
    constructor(...) = auto;
    
    // Or manually create two constructors
    constructor() {
        this.name = name;
        this.age = 0; 
    }
    
    constructor(Person p) {
        this.name = p.name;
        this.age = p.age;
    }
    
    constructor(name: string, age: int) {
        // Note: the values are copied because the parameters are immutable.
        this.name = name;
        this.age = age;
    }
    
    getName(): string => this.name;
    
    getAge(): int => this.age;
    
    setName(name: string): void {
        this.name = name;
    }
    
    setAge(age: int): void {
        this.age = age;
    }
    
    // Example function explicitly specifing public visibility
    pub fn setNameAndAge(name: string, age: int): void {
        this.name = name;
        this.age = age;
    }
   
    // Private function example
    priv fn reset() {
        this.name = '';
        this.age = 0;
    }
}
```

## Simple templated container class

```ezscript
template<T>
class Container {
    var data: mut array; 
    constructor() = empty;
    
    empty(): bool => this.data.empty();
    
    length(): int => this.data.length();
    
    push(item: T): void {
        this.data.append(item);
    }
    
    at(index: int): T {
        if (index > this.length()) {
            throw new OutOfBoundsException(
                'Out of bounds exception, index is greater than length of data'
            );
        }
        
        return this.data[index];
    }
    
    pop(): T {
        if (this.empty()) {
            throw new Exception('Cannot pop off an empty container');
        }
        
        var item = this.data.end();
        this.data.pop();
        return item;
    }
}
```
