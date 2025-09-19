# Fifty-fifty-ish

> A service that splits your bill almost in half.

# Problem
When splitting a bill a dreaded "penny problem" emerges. Who gets stuck with that extra cent when $27.27 can't be split evenly?
```
Fuel:     $100.01  →  Payer A: $50.01, Payer B: $50.00 ❌ (unfair)
Insurance: $27.27  →  Payer A: $13.64, Payer B: $13.63 ❌ (unfair) 
Oil:      $117.17  →  Payer A: $58.59, Payer B: $58.58 ❌ (unfair)
```

# Solution
This solution uses [Largest remainder method](https://electowiki.org/wiki/Largest_remainder_method) to distribute extra pennies fairly(ish).
It accumulates remainders across each expense and splits them in half. In the event of .01 remainder it will transfer the remainder to first payer. 


# Data structure

```sql
expenses:
├── id
├── payer_a_name
├── payer_b_name
├── title
├── amount (int)  
├── expense_date
└── timestamps
```

# ScratchBook 

- Should we store remainders/split in database or should we calculate per row upon request? 
- Should we support more than 2 participants?
- Store amount as decimal/string/int?
