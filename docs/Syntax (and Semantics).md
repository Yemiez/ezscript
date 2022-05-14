# Syntax (and Semantics)

## Concepts

* Statements are terminated with a `;` character.
* Variables are immutable by default.
* A pointer can only be created from a mutable variable.
* Manual memory allocation can be done through unsafe functions `malloc` and `free`.

## Variables

#### Immutable variable declaration

```ezscript
module Examples.ImmutableVariables;

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
module Examples.MutableVariables;

var variable: mut type = ...;
// Examples
var firstName: mut string = "John";
var lastName: mut string = "Doe";
var age: mut int = 23;

age++; // This is okay
```

### Smart pointer variable declarations

```ezscript
module Examples.SharedPtr;

// syntax: var variable: type = ...;
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
module Examples.DifferentPtrs;

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

### Unsafe pointers and unsafe memory management

```ezscript
module Examples.UnsafeMemoryAllocation;
import Random from System.Crypto;

// Allocate 256 bytes
var bytes: mut *byte = malloc(256);

for (i = 0; i < 256; ++i) {
    // Set each byte to a random value between 0 and 10
    *(bytes + i) = Random.integer(0, 10);
}

// IMPORTANT, you must always manually free the memory
free(bytes);
```

## Functions

## Concepts

* Primitive immutable variables are passed to function by value.
* Primitive mutable variables are passed to function by pointer/reference.
* Functions can be templated

## Functions outside of classes

Global (in current module) function:

```ezscript
module Examples.GlobalFunction;

fun add(a: int, b: int): int {
    ret a + b;
}
```

Lambda functions:

```ezscript
module Examples.LambdaFunctions;

var events: object = {}

fn listen(eventName: string, callback: callable): void {
    if (events.has(eventName)) {
        events[eventName] = []
    } 
}

fn trigger(eventName: string, eventData: object): void {
    if (!events.has(eventName)) {
        ret;
    }
    
    events[eventName].forEach(fn (callback): void => {
        callback(eventData); 
    });
}

listen('click', fn (data: object) {
    // Just an example...
    __context.logger.debug('event data', data);
});

// Will call all the listeners registered through listener.
trigger('click',  {
    target: 'window',
});
```

Templated function:

```ezscript
module Examples.TemplatedFunction;

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
module Examples.SimpleClass;
export Person; // or simply declare class as "pub class Person"

class Person {
    var name: mut string;
    var age: mut int;
    
    // Automatically generate appropiate constructors (same as below)
    constructor() = auto;
    
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
module Examples.TemplatedClass;

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

## Interfaces

### Concepts

* An interface is an abstract type. It can never be instantiated.
* Interfaces are used to describe how a certain Feature or Type should behave.

### Simple interface

```ezscript
module Examples.SimpleInterface;
import Console from System.IO;

pub interface Visitor {
    // Pass by ref
    handle(expr: &Expression): void;
}

pub interface Expression {
    visit(visitor: Visotor): void;
}

pub class LiteralExpression implements Expression {
    var value: int;
    constructor() = auto;
    
    // Note how we can change the type
    visit(visitor: &LiteralVisotor): void {
        visitor.handle(this); 
    }
    
    getValue() => this.value;
}

pub class LiteralVisitor implements Visitor {
    handle(expr: &Expression): void {
        Console.println("Literal value: {}", expr.getValue()); 
    }
}

var visitor = new LiteralVisitor();
var expression = new LiteralExpression(25);

expression.visit(visitor); // Prints "Literal value: 25" to console stdout
```
