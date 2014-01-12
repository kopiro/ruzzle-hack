# RuzzleHack: RuzzleSolver in PHP

### Requirements

* PHP 5.4>=
* If you install [**HHVM**](http://www.hhvm.com/), PHP run faster: so replace `php` with `hhvm`

### Installation

Clone this repository: `git clone https://github.com/kopiro/RuzzleHack.git`

Now, find some *.txt files that contains words for your language, EOL delimited.

Create a directory `{LANG}.dict` in RuzzleHack, put inside your txt files.

Now, run `php gendict.php {LANG}`.

### Usage

Use simply with: `php print.php {LANG} {LINEARMATRIX}`

`{LINEARMATRIX}` is the Ruzzle matrix, linearized.

### Dictionaries yet ready

* **Italian**: [http://cl.ly/0W0z2h3g3F0c](http://cl.ly/0W0z2h3g3F0c)

#### Developers

[Flavio De Stefano](https://github.com/kopiro)

[Stefano Azzolini](https://github.com/lastguest)