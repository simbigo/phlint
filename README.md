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
<EXPRESSION> ::= TERM (PM_OPERATOR TERM)*
<TERM> ::= FACTOR (MD_OPERATOR FACTOR)*
<FACTOR> ::= INTEGER
<PM_OPERATOR> ::= "+"|"-"
<MD_OPERATOR> ::= "*"|"/"
<INTEGER> ::= DIGIT(DIGIT)*
<DIGIT> ::= 0|1|2|3|4|5|6|7|8|9
```

### Expressions

