import os                           # Käyttöjärjestelmä
import RPi.GPIO as GPIO             # Raspberry GPIO
import time                         # Ajanhallinta
import requests                     # Nettisivun selailua varten
import picamera                     # Raspberry kamera (PiCamera())
import ftplib                       # FTP Latausta varten
import datetime                     # Päivämäärän luontia varten
import subprocess                   # Remote-Terminal varten
import threading                    # Timer:iä varten
from PIL import Image               # Jotta voidaan vaihtaa kuvan kokoa
from resizeimage import resizeimage # Jotta voidaan vaihtaa kuvan kokoa

camera = picamera.PiCamera() # PiCamera
pin = 18                     # Liiketunnistimen PIN -> 18

GPIO.setmode(GPIO.BCM)      
GPIO.setup(pin, GPIO.IN)

print "Paina Ctrl+C lopetakseen."
time.sleep(2)
print "Valmis..";

# Remote Terminal Koodi
def updates():
    threading.Timer(3.0, updates).start() # timer :: toistuu aina 3 sek välein
    
    # hakee/etsii tulevia komentoja
    link = 'https://marcosraudkett.com/mvrclabs/code/scripts/admin/script/pi-terminal/APPKEY/'
    f = requests.get(link) # lukee sivulla olevan tekstin
    #print f.text
    
    # toteutetaan komento raspberrillä
    shell = subprocess.Popen(f.text, shell=True, stdout=subprocess.PIPE, stderr=subprocess.STDOUT).stdout
    response = shell.read() # luetaan shell
    #print response
    
    # skripti joka palauttaa viestin äskeiselle komennolla joka toteutettiin
    link_response = 'https://marcosraudkett.com/mvrclabs/code/scripts/admin/script/pi-terminal-response/APPKEY/&response='+response
    f2 = requests.get(link_response) # lukee sivulla olevan tekstin
    
updates()

# Liikettä Havaittu
try:
    while True:
        if (GPIO.input(pin)==1):
            print "Liiketta Havaittu"
            
            # luodaan päivämäärä + kellonaika NYT
            date = datetime.datetime.now().strftime("%m-%d-%Y_%H%M%S")
            
            #i = 0
            #while os.path.exists("/home/pi/Desktop/motions/" + date + ".png"):
            #    i += 1
            #abc = i
            
            # ottaa kuvan
            camera.capture('/home/pi/Desktop/motions/' + date + '.png')

            # tässä muutetaan kuvan kokoa jotta dashboard toimisi mahdollisimman nopeasti
            fd_img = open('/home/pi/Desktop/motions/' + date + '.png', 'r')
            img = Image.open(fd_img)
            img = resizeimage.resize_cover(img, [480, 360])
            img.save('/home/pi/Desktop/havainnot/' + date + '.png', img.format)
            fd_img.close()
            
            camera.vflip = True

            # FTP Lataus
            session = ftplib.FTP('website.com','FTP KÄYTTÄJÄNIMI','FTP SALASANA') # FTP Tiedot
            file = open('/home/pi/Desktop/motions/' + date + '.png', 'rb')                 
            session.storbinary('STOR ' + date + '.png', file)     
            file.close()                                   
            session.quit()
            
            # skripti joka tallentaa tietokantaan ja myös ilmoittaa käyttäjille
            requests.get('https://marcosraudkett.com/mvrclabs/code/scripts/admin/script/service.php?app_key=APPKEY&img=' + date + '.png').content
            
            os.remove('/home/pi/Desktop/motions/' + date + '.png')
        
        # odottaa 4 sek ennenkuin voi havaita uudelleen.
        time.sleep(4)
except KeyboardInterrupt:
    print "Dead"
    GPIO.cleanup()
