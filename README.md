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
<PROGRAM>           ::= CLASS_DEFINITION|STATEMENT_LIST
<STATEMENT_LIST>    ::= STATEMENT ";" (STATEMENT ";")*
<STATEMENT>         ::= SETTER|CALL_FUNCTION
<SETTER>            ::= VARIABLE "=" EXPRESSION
<CALL_FUNCTION>     ::= FUNCTION_NAME "(" NUMBER ")"
<VARIABLE>          ::= WORD
<FUNCTION_NAME>     ::= WORD
<EXPRESSION>        ::= TERM (PM_OPERATOR TERM)*
<TERM>              ::= FACTOR (MD_OPERATOR FACTOR)*
<FACTOR>            ::= NUMBER|("(" EXPRESSION ")")
<PM_OPERATOR>       ::= "+"|"-"
<MD_OPERATOR>       ::= "*"|"/"
<NUMBER>            ::= INTEGER|FLOAT
<INTEGER>           ::= DIGIT(DIGIT)*
<FLOAT>             ::= INTEGER(.INTEGER)
<DIGIT>             ::= 0|1|2|3|4|5|6|7|8|9
<WORD>              ::= (ALPHA|"_")(ALPHA|"_")*
<ALPHA>             ::= A..z
```

### Whitespaces

Ignore whitespace if it isn't a part of a string.

```
4     +    4
```
equal:
```
4+4
```

### Expressions

```
3 + 2;
6 - 4;
2 * 5;
6 / 3;
2 + 2 * 2 - 6;
(2 + 2 * 3) - 10 * (2 * (6 + 4));
```


### Variables

```
a = 3 + 4;
b = 2 * a
```

### Functions

```
a = 2;
sum = 3 + a;
print(sum);
print(sum * 8);
```

### Classes

class Drink = {}
