# for every file in vector/ (recursive), convert to png and save in raster/
import os
import sys
import subprocess

input = "vector/"
output = "raster/"

def convert(f, directory):
    if f.endswith(".svg"):
        outputFile = f.replace(".svg", ".png")
        outputDirectory = directory.replace("vector", "raster")
        if not os.path.exists(outputDirectory):
            os.makedirs(outputDirectory)
        print("Converting " + f + " to " + outputFile)
        eva = ["inkscape", "--export-type=png", "--export-filename=" + outputDirectory + "/" + outputFile, directory + "/" + f]
        print(eva)
        subprocess.call(eva)
        print("Done.")
    else:
        print("Skipping " + f + "...")

# recursively go through every file in vector/ and convert to png (including subdirectories)
for root, dirs, files in os.walk(input):
    for f in files:
        convert(f, root)