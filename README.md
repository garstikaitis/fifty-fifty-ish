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
├── party_a_name
├── party_b_name
├── title
├── amount (int)  
├── occured_at
```

# ScratchBook 

- Should we store remainders/split in database or should we calculate per row upon request?
  - We will calculate remainders for each chunk of expenses at runtime. This potentially could be changed when dealing with massive datasets. The possible changes are:
    - Run export expenses job in queue and send result via mail/slack/etc.
    - Store amounts per expense in database
- Should we support more than 2 drivers?
  - Current implementation assumes that there are 2 drivers and they are named "Driver #1" and "Driver #2". 
- Should we store amount as decimal/string/int?
  - Every amount is stored as integer. this way we are sure we don't get floating point errors and still keep the possibility to use DB aggregate methods (SUM, AVG)
  - In order to support different currencies that use different number of floating points than 2 - improvement could be to add a currency column to expenses table and adjust `Currency` class
