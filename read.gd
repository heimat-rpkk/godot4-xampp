extends Node

# Luetaan tietokannasta, data palautuu JSON-muodossa

@onready var pisteet = $Label # This should be a Label or RichTextLabel
@onready var http_request = HTTPRequest.new()

func _ready():
	add_child(http_request)
	http_request.request_completed.connect(self._on_leaderboard_received)


func get_leaderboard():
	var url = "http://localhost/godot_test/get_leaderboard.php"
	var error = http_request.request(url)
	if error != OK:
		print("Error sending request: ", error)
	else:
		print("Requesting leaderboard...")


func _on_leaderboard_received(_result, response_code, _headers, body):
	#virheilmoitus, jos sellainen tulee
	if response_code != 200:
		pisteet.text = "Virhe: HTTP " + str(response_code)
		return
	var json_text = body.get_string_from_utf8()
	var data = JSON.parse_string(json_text)
	if typeof(data) == TYPE_ARRAY:
		show_leaderbord(data)
	else:
		pisteet.text = "Leaderboardin lataus epäonnistui"


func show_leaderbord(data: Array):
	var container = %VBoxContainer
	# tyhjennetään container
	for child in container.get_children():
		child.queue_free()
	# Otsikko
	var title = Label.new()
	title.text = "Top 10"
	title.add_theme_font_size_override("font_size", 26)
	container.add_child(title)
	# Luetaan datasta rivi kerrallaan ja tehdään uusi label
	for i in range(data.size()):
		var label = Label.new()
		var row = data[i]
		var rank = i + 1
		var username = row["username"]
		var score = int(row["score"]) # Force integer
		label.text = "%d. %s - %d" % [rank, username, score]
		container.add_child(label)
		
	
	

func _on_read_button_pressed():
	get_leaderboard()
