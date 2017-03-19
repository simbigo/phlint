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
<PROGRAM>                 ::= (<CLASS_DEFINITION>|<IF_CONDITION>|<STATEMENT_LIST>)*
<CLASS_DEFINITION>        ::= "class" <IDENTIFIER> "=" "{" "}"
<IF_CONDITION>            ::= "if" <CONSTRUCTION_CONDITION> "{" <PROGRAM> "}" ("else" "{" <PROGRAM> "}")?
<CONSTRUCTION_CONDITION>  ::= <EXPRESSION> <COMPARE_OP> <EXPRESSION>
<STATEMENT_LIST>          ::= <STATEMENT> ";" (<STATEMENT> ";")*
<STATEMENT>               ::= <SETTER>|<CALL_FUNCTION>
<COMPARE_OP>              ::= ">"|"<"|"=="|">="|"<="|"!="
<SETTER>                  ::= <VARIABLE> "=" <EXPRESSION>
<CALL_FUNCTION>           ::= <FUNCTION_NAME> "(" <NUMBER> ")"
<VARIABLE>                ::= <IDENTIFIER>
<FUNCTION_NAME>           ::= <IDENTIFIER>
<EXPRESSION>              ::= <TERM> (<PM_OPERATOR> <TERM>)*
<TERM>                    ::= <FACTOR> (<MD_OPERATOR> <FACTOR>)*
<FACTOR>                  ::= <NUMBER>|("(" <EXPRESSION> ")")
<PM_OPERATOR>             ::= "+"|"-"
<MD_OPERATOR>             ::= "*"|"/"
<NUMBER>                  ::= <INTEGER>|<FLOAT>
<INTEGER>                 ::= <DIGIT>(<DIGIT>)*
<FLOAT>                   ::= <INTEGER>("."<INTEGER>)
<DIGIT>                   ::= 0|1|2|3|4|5|6|7|8|9
<IDENTIFIER>              ::= (<ALPHA>|"_")(<ALPHA>|"_")*
<ALPHA>                   ::= A..z
```

### Whitespaces

Ignore whitespace if it isn't a part of a string.

```c
4     +    4
```
equal:
```c
4+4
```

### Expressions

```c
3 + 2;
6 - 4;
2 * 5;
6 / 3;
2 + 2 * 2 - 6;
(2 + 2 * 3) - 10 * (2 * (6 + 4));
```


### Variables

```c
a = 3 + 4;
b = 2 * a
```

### Functions

```c
a = 2;
sum = 3 + a;
print(sum);
print(sum * 8);
```

### Flow control

```c
foo = 11
if foo > getValue(5 + 5) {
    bar = 1;
} else {
    bar = 0;
}

if bar == 0 {
    foo = foo + 4;
}
```

### Classes

```c
class Drink = {}
```
