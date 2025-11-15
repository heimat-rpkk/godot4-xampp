extends Node

# Tallennus tietokantaan palvelimen kautta. 
# tieto välitetään palvelimelle JSON-muodossa tai 

@onready var http_request: HTTPRequest = HTTPRequest.new()


func _ready():
	# Create an HTTP request node and connect its completion signal.
	add_child(http_request)
	http_request.request_completed.connect(self._http_request_completed)

func save_json(data: Dictionary) -> void:
	# Luo POST-pyyntö JSON-datalla
	var url := "http://localhost/godot_test/save.php"
	var body := JSON.stringify(data)
	var headers := ["Content-Type: application/json"]

	# Lähetetään pyyntö http_request-nodella (sen täytyy olla olemassa!)
	var error := http_request.request(url, headers, HTTPClient.METHOD_POST, body)
	if error != OK:
		print("Virhe HTTP-pyynnössä: %s" % error)
	else:
		print("Sending data...")


# Called when the HTTP request is completed.
func _http_request_completed(_result, response_code, _headers, body):
	print("Response code:", response_code)
	var text = body.get_string_from_utf8()
	var json = JSON.parse_string(text)
	print("Serverin vastaus:", json)


func _on_save_button_pressed():
	%InfoLabel.text = ""
	var username = %NameLineEdit.text.strip_edges()
	var score = %ScoreLineEdit.text.strip_edges()
	if username == "":
		%InfoLabel.text = "Nimi ei saa olla tyhjä!"
		return
	if not score.is_valid_int():
		%InfoLabel.text = "Pisteet tulee olla numeromuodossa!"
		return
		
	save_json({"username": username, "score": score})
