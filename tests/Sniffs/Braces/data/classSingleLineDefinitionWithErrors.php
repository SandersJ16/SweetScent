<?php

/**
 * There are 12 total errors in this file
 */

# In line empty class missing a space

class Foo1{}

class Foo2 extends Bar{}

class Foo3 implements Foo{}

class Foo4 extends Bar implements Bar{}

# In line empty class miss

// class Fow1 {  }

// class Fow2 extends Bar {  }

// class Fow3 implements Foo {  }

// class Fow4 extends Bar implements Bar {  }


# Brackets following Line in empty class

class Few1
{
}

class Few2 extends Bar
{
}

class Few3 implements Foo
{
}

class Few4 extends Bar implements Bar
{
}

# Multi-line brackets

class Fu1 {
}

class Fu2 extends Bar {

}

class Fu3 implements Foo {

}

class Fu4 extends Bar implements Bar {

}

# Single line brackets on line after definition of empty class

class Fi1
{}

class Fi2 extends Bar
{}

class Fi3 implemnts Foo
{}

class Fi4 extends Bar implements Bar
{}
