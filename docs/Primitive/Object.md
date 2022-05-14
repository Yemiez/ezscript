# Objects in EzScript

An object is really simple to create, and can be done like such:

```ezscript
var obj: object = {
    key: 'value',
    otherKey: 'otherValue',
    'key with spaces': 15,
}

// With this object you can later access these values with the access to member operator.
obj.key; // returns 'value'
obj.otherKey; // return 'otherValue'
obj['key with spaces']; // returns 15

// But since all values/objects in ezscript are immutable by default, we cannot change or modify
```
