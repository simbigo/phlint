# Phlint

Phlint is the interpreter of my fictional programming language. To write some
language using PHP is crazy idea, but... why not? What do we know about Phlint now?
Only its name - **Ph**p **L**anguage **Int**erpreter.

The interpreter has two mode: interactive and file mode.
 
 
File mode:
```sh
$ phlint pli/main.pli
```

Interactive mode:
```sh
$ phlint
[phlint]: print "Hello"
Hello
[phlint]: exit
```

## Backus–Naur form

```
program                  = (declaration | class_declaration)*
                          
declaration              = statement
                         | variable_declaration 
                         | function_declaration 
      
statement                = statement_expr
                         | if_statement 
                         | while_statement 
                         | for_statement
                         | return_statement
           
class_declaration        = "class" identifier "=" (class_block | extend_block)
class_block              = "{" class_body "}"
extend_block             = identifier "." "extend" "(" "{" class_body "}" ")"
class_body               = (prop_declaration | method_declaration)*
prop_declaration         = "prop" modifiers var_declaration
modifiers                = ":" visible_mod (":" "static")?
method_declaration       = "prop" modifiers function_declaration
visible_mod              = "private" | "protected" | "public"
   
variable_declaration     = identifier "=" statement_expr

function_declaration     = "func" identifier function "=" "{" declaration* "}"
function                 = identifier "(" arguments?  ")"
arguments                = argument ("," argument)*
argument                 = expression

statement_expr           = expression ";"
expression               = (term | variable | function) (plus_minus (term | variable | function))*
term                     = factor (mul_div factor)*
factor                   = number | expression 

string                   = ('"' ...* '"' ) | ("'" ...* "'" )
number                   = integer | float
float                    = integer "." integer
integer                  = digit ("." digit)*
digit                    = "0" ... "9"
indentifier              = alpha | "_" (alpha | "_")*
alpha                    = "A" ... "z"
compare                  = ">" | "<" | "==" | ">=" | "<=" | "!="
plus_minus               = "+" | "-"
mul_div                  = "*" | "/"
```

### Whitespaces

All spaces are ignored if they are not part of the string

```java
4     +    4
```
equal:
```java
4+4
```

### Expressions

```java
3 + 2;
6 - 4;
2 * 5;
6 / 3;
2 + 2 * 2 - 6;
(2 + 2 * 3) - 10 * (2 * (6 + 4));
```

### Variables

```java
a = 3 + 4;
b = 2 * a
```

### Functions

```java
func sum(a, b) = {
    func inc(c) = {
        return c + 1;
    }
    return inc(a) + inc(b);
}

result = sum(4, 5)
print(result);
```

### Control flow

```java
foo = 11
if foo > getValue(5 + 5) {
    bar = 1;
} else {
    bar = 0;
}

if bar == 0 {
    foo = foo + 4;
}

i = 10
while (i > 0) {
    print(i);
    i = i - 1;
}
```

### Classes

```c
class Animal = {
   prop:protected age = 4;
   
   method:public:static say() = {
        print("Rrrrrr");
   }
}

class Cat = Animal.extend({
    method:public getAge() = {
        return this.age;
    }
    
    method:public:static say() = {
        parent.say();
    }
})
```
